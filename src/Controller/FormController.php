<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FormController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation, [
            'allow_extra_fields' => true
        ]);

        $form->handleRequest($request);

        // 2. Traitement du formulaire soumis
        if ($form->isSubmitted() && $form->isValid()) {



            // --- A. GESTION DE LA DATE ET L'HEURE ---
            // Le champ date est géré par Symfony, mais l'heure est un champ HTML manuel
            $timeSlot = $request->request->get('time_slot');

            if (!$timeSlot) {
                $this->addFlash('danger', 'Erreur : Veuillez sélectionner un horaire valide.');
                return $this->redirectToRoute('app_reservation');
            }

            // On fusionne l'heure (ex: "14:30") avec la date choisie
            $dateRdv = $reservation->getDateRdv();
            [$hours, $minutes] = explode(':', $timeSlot);
            $dateRdv->setTime($hours, $minutes);

            // --- B. CALCULS DE SÉCURITÉ (Prix & Durée) ---
            // On ne fait jamais confiance au prix envoyé par le JavaScript. On le recalcule.
            $totalPrice = 0;
            $rawMinutes = 0;

            // 1. Somme des services cochés
            foreach ($reservation->getServices() as $service) {
                $totalPrice += $service->getPrice();
                $rawMinutes += $service->getDuration();
            }

            // 2. Option Dépose (Champ manuel du formulaire)
            if ($form->has('includeDepose') && $form->get('includeDepose')->getData()) {
                $totalPrice += 10; // Prix fixe dépose
                $rawMinutes += 20; // Durée fixe dépose
            }

            // 3. Règle Métier : Arrondi au 1/4 d'heure supérieur (15 min)
            $finalDuration = ceil($rawMinutes / 15) * 15;


            // --- C. REMPLISSAGE DE L'ENTITÉ ---
            $reservation->setTotalPrice($totalPrice);
            $reservation->setTotalDuration($finalDuration);
            $reservation->setReference('RDV-' . strtoupper(uniqid()));
            $reservation->setStatus('CONFIRMED');


            // --- D. GESTION UTILISATEUR VS INVITÉ ---
            if ($this->getUser()) {
                // Si connecté, on lie le compte
                $reservation->setUser($this->getUser());
            } else {
                // Si invité, on stocke les infos dans le commentaire (ou des champs dédiés si créés)
                $guestInfo = sprintf(
                    " [INVITÉ: %s %s - %s - %s]",
                    $form->get('guestFirstname')->getData(),
                    $form->get('guestLastname')->getData(),
                    $form->get('guestEmail')->getData(),
                    $form->get('guestPhone')->getData()
                );
                // On ajoute ces infos au commentaire existant
                $reservation->setComment($reservation->getComment() . $guestInfo);
            }

            // F. GESTION PROMO (Serveur)
            // On récupère le code saisi dans le formulaire
            $promoCode = $form->get('promoCode')->getData();

            if ($promoCode) {

                $promo = $promoRepo->findOneBy(['code' => $promoCode]);

                if ($promo && $promo->isActive()) {
                    $percentage = $promo->getPercentage();
                    $discountAmount = ($totalPrice * $percentage) / 100;
                    $totalPrice = $totalPrice - $discountAmount;

                    $reservation->setComment($reservation->getComment() . " | PROMO: " . $promoCode);
                }
            }

            $reservation->setTotalPrice($totalPrice);

            // --- E. ENREGISTREMENT BDD ---
            $em->persist($reservation);
            $em->flush();

            // --- F. REDIRECTION VERS SUCCÈS ---
            // On prépare les variables pour l'affichage de confirmation
            $clientName = $this->getUser()
                ? $this->getUser()->getFirstname()
                : $form->get('guestFirstname')->getData();

            $clientEmail = $this->getUser()
                ? $this->getUser()->getEmail()
                : $form->get('guestEmail')->getData();

            return $this->render('booking/success.html.twig', [
                'name' => $clientName,
                'email' => $clientEmail,
                'date' => $reservation->getDateRdv()->format('d/m/Y à H:i')
            ]);
        }

// AJOUT : Définition des horaires (Simulé ici, pourrait venir de la BDD)
        $openingHours = [
            'Lundi'    => '09:00 - 18:00',
            'Mardi'    => '09:00 - 18:00',
            'Mercredi' => '09:00 - 18:00',
            'Jeudi'    => '09:00 - 18:00',
            'Vendredi' => '09:00 - 19:00', // Nocturne !
            'Samedi'   => '09:00 - 14:00', // Journée courte
            'Dimanche' => 'Fermé',
        ];

        return $this->render('booking/index.html.twig', [
            'form' => $form->createView(),
            'openingHours' => $openingHours,
        ]);
    }

    #[Route('/api/check-promo', name: 'api_check_promo', methods: ['POST'])]
    public function checkPromo(Request $request, PromotionRepository $promoRepo): JsonResponse
    {
        // 1. On récupère le code envoyé par le JS (format JSON)
        $data = json_decode($request->getContent(), true);
        $code = $data['code'] ?? '';

        // 2. On cherche dans la BDD
        $promo = $promoRepo->findOneBy(['code' => $code]);

        // 3. Vérifications (Existe ? Actif ?)
        if (!$promo) {
            return $this->json(['valid' => false, 'message' => 'Code inconnu']);
        }

        if (!$promo->isActive()) { // Supposons que vous ayez un champ 'active'
            return $this->json(['valid' => false, 'message' => 'Ce code a expiré']);
        }

        // 4. Succès : on renvoie le pourcentage
        return $this->json([
            'valid' => true,
            'percentage' => $promo->getPercentage(),
            'message' => 'Code appliqué : -' . $promo->getPercentage() . '%'
        ]);
    }


}
