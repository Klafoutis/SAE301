<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Reservation;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;

final class FormController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation,[
            'allow_extra_fields' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // --- LOGIQUE METIER SIMPLE ---

            // 1. Gestion User vs Invité
            if ($this->getUser()) {
                $reservation->setUser($this->getUser());
            } else {
                // Ici on récupérerait les champs "guest" du formulaire pour créer un user ou juste stocker
                // Pour la démo, on laisse vide ou on remplit un champ "commentaire" avec les infos
                $guestInfo = $form->get('guestFirstname')->getData() . ' ' . $form->get('guestLastname')->getData();
                $reservation->setComment($reservation->getComment() . " [INVITÉ: $guestInfo]");
            }

            // 2. Calculs finaux (Prix, Durée)
            $totalPrice = 0;
            $totalDuration = 0;
            foreach ($reservation->getServices() as $service) {
                $totalPrice += $service->getPrice();
                $totalDuration += $service->getDuration();
            }
            // Si dépose (champ non mappé)
            if ($form->get('includeDepose')->getData()) {
                $totalPrice += 10;
                $totalDuration += 20;
            }

            $reservation->setTotalPrice($totalPrice);
            $reservation->setTotalDuration($totalDuration);
            $reservation->setReference('RDV-' . uniqid());
            $reservation->setStatus('CONFIRMED');

            // 1. On récupère la date choisie (ex: 2025-12-12 00:00:00)
            $dateRdv = $reservation->getDateRdv();

            // 2. On récupère l'heure envoyée par le select HTML (ex: "14:30")
            $timeString = $request->request->get('time_slot');

            if ($timeString) {
                // On sépare les heures et les minutes
                [$hours, $minutes] = explode(':', $timeString);

                // On met à jour l'objet Date avec la bonne heure
                $dateRdv->setTime($hours, $minutes);
            }
            // 3. Sauvegarde
            $em->persist($reservation);
            $em->flush();

            // 4. Page de succès
            // On récupère le prénom pour l'affichage
            $name = $this->getUser() ? $this->getUser()->getFirstname() : $form->get('guestFirstname')->getData();
            $email = $this->getUser() ? $this->getUser()->getEmail() : $form->get('guestEmail')->getData();

            return $this->render('booking/success.html.twig', [
                'name' => $name,
                'email' => $email,
                'date' => $reservation->getDateRdv()->format('d/m/Y à H:i')
            ]);
        }
        // AJOUT : Définition des horaires (Simulé ici, pourrait venir de la BDD)
        $openingHours = [
            'Lundi'    => '09:00 - 19:00',
            'Mardi'    => '09:00 - 19:00',
            'Mercredi' => '09:00 - 19:00',
            'Jeudi'    => '09:00 - 19:00',
            'Vendredi' => '09:00 - 19:00', // Nocturne !
            'Samedi'   => '09:00 - 14:00', // Journée courte
            'Dimanche' => 'Fermé',
        ];

        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation, [
            'allow_extra_fields' => true // 1. Autoriser les champs manuels (time_slot)
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 2. Récupérer l'heure manuelle depuis la Requête (pas depuis le Form)
            $timeSlot = $request->request->get('time_slot');

            if (!$timeSlot) {
                // Gestion d'erreur si l'heure est vide
                $this->addFlash('error', 'Veuillez sélectionner un horaire.');
                return $this->redirectToRoute('app_reservation');
            }

            // 3. Fusionner Date + Heure
            // $reservation->getDateRdv() contient ex: "2025-12-12 00:00:00"
            // $timeSlot contient ex: "14:30"
            $dateRdv = $reservation->getDateRdv();
            [$hours, $minutes] = explode(':', $timeSlot);
            $dateRdv->setTime($hours, $minutes);

            // Calculs (Prix, Durée, etc.)
            // ... (remettre votre code de calcul ici) ...

            $em->persist($reservation);
            $em->flush();

            return $this->render('booking/success.html.twig', [
                // ...
            ]);
        }

        $stepOpen = ($form->isSubmitted() && !$form->isValid()) ? 4 : 1;

        return $this->render('booking/index.html.twig', [
            'form' => $form->createView(),
            'openingHours' => $openingHours,
            'stepOpen' => $stepOpen
        ]);
    }
}
