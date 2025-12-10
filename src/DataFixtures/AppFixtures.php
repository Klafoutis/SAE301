<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Service;
use App\Entity\User;
use App\Entity\Reservation;
use App\Entity\Promotion;
use App\Entity\Unavailability;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Initialisation de Faker (en français)
        $faker = Factory::create('fr_FR');

        // --- 1. LES CATÉGORIES ---
        $categoriesData = ['Soins des Mains', 'Beauté des Pieds', 'Dépose & Réparations', 'Nail Art'];
        $categoriesEntities = [];

        foreach ($categoriesData as $catName) {
            $category = new Category();
            $category->setName($catName);
            // Création d'un slug simple (ex: Soins des Mains -> soins-des-mains)
            $category->setSlug(strtolower(str_replace(' ', '-', $catName)));

            $manager->persist($category);
            $categoriesEntities[$catName] = $category; // On garde en mémoire pour lier les services
        }

        // --- 2. LES SERVICES ---
        $servicesData = [
            // [Nom, Prix, Durée (min), Catégorie]
            ['Pose Vernis Semi-permanent', 35, 45, 'Soins des Mains'],
            ['Pose Complète Gel (Chablons)', 65, 120, 'Soins des Mains'],
            ['Remplissage Gel', 45, 90, 'Soins des Mains'],
            ['Manucure Russe (Nettoyage)', 25, 30, 'Soins des Mains'],
            ['Pédicure Complète + Vernis', 55, 60, 'Beauté des Pieds'],
            ['Dépose Semi-permanent', 15, 20, 'Dépose & Réparations'],
            ['Réparation ongle cassé', 5, 10, 'Dépose & Réparations'],
            ['Nail Art simple (par doigt)', 2, 5, 'Nail Art'],
            ['Babyboomer', 10, 15, 'Nail Art'],
        ];

        $servicesEntities = [];

        foreach ($servicesData as $data) {
            $service = new Service();
            $service->setTitle($data[0]);
            $service->setPrice($data[1]);
            $service->setDuration($data[2]);
            $service->setDescription($faker->sentence(10));
            $service->setActive(true);
            $service->setCategory($categoriesEntities[$data[3]]);

            $manager->persist($service);
            $servicesEntities[] = $service;
        }

        // --- 3. LES UTILISATEURS (USER) ---

        // A. Création d'un Admin (Vous !)
        $admin = new User();
        $admin->setEmail('admin@ongles.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setFirstname('Sophie');
        $admin->setLastname('Lartiste');
        $admin->setPhono('0123456789');
        $admin->setAddress('10 rue de la Paix');
        $admin->setZipcode('10000');
        $admin->setCity('Troyes');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $manager->persist($admin);

        // B. Création de clients fictifs
        $usersEntities = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail("client$i@mail.com");
            $user->setFirstname($faker->firstNameFemale());
            $user->setLastname($faker->lastName());
            $user->setPhono($faker->phoneNumber());
            $user->setAddress($faker->streetAddress());
            $user->setZipcode(str_replace(' ', '', $faker->postcode())); // Nettoyage espace
            $user->setCity($faker->city());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));

            $manager->persist($user);
            $usersEntities[] = $user;
        }

        // --- 4. LES RÉSERVATIONS ---
        for ($i = 0; $i < 20; $i++) {
            $reservation = new Reservation();

            // On prend un utilisateur au hasard
            $client = $faker->randomElement($usersEntities);
            $reservation->setClient($client);

            // Infos client (copie au moment de la commande)
            $reservation->setVisitAddress($client->getAddress() . ' ' . $client->getCity());

            // Date aléatoire (entre -1 mois et +1 mois)
            $date = $faker->dateTimeBetween('-1 month', '+1 month');
            // On force des horaires réalistes (9h - 18h)
            $date->setTime(rand(9, 17), [0, 15, 30, 45][rand(0, 3)]);
            $reservation->setDateRdv($date);

            $reservation->setReference('RDV-' . strtoupper(uniqid()));
            $reservation->setStatus($date < new \DateTime() ? 'COMPLETED' : 'CONFIRMED');

            // Ajout de 1 à 3 services au hasard
            $randomServices = $faker->randomElements($servicesEntities, rand(1, 3));
            $totalPrice = 0;
            $totalDuration = 0;

            foreach ($randomServices as $s) {
                $reservation->addService($s);
                $totalPrice += $s->getPrice();
                $totalDuration += $s->getDuration();
            }

            $reservation->setTotalPrice($totalPrice);
            $reservation->setTotalDuration($totalDuration);
            $reservation->setComment($faker->optional()->sentence());

            $manager->persist($reservation);
        }

        // --- 5. CODES PROMO ---
        $promo = new Promotion();
        $promo->setCode('BIENVENUE10');
        $promo->setPercentage(10);
        $promo->setAtive(true);
        $manager->persist($promo);

        $promoExpired = new Promotion();
        $promoExpired->setCode('NOEL2023');
        $promoExpired->setPercentage(20);
        $promoExpired->setAtive(false);
        $manager->persist($promoExpired);

        // --- 6. INDISPONIBILITÉS ---
        $unavailability = new Unavailability();
        $start = new \DateTime('+2 days 12:00:00');
        $end = new \DateTime('+2 days 14:00:00');
        $unavailability->setStartDate($start);
        $unavailability->setEndDate($end);
        $unavailability->setReason('Déjeuner pro');
        $manager->persist($unavailability);

        $manager->flush();
    }
}
