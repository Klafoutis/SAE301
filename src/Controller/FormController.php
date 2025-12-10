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
        $form = $this->createForm(ReservationType::class, $reservation);

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

        return $this->render('form/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
