<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReservationController extends AbstractController
{
    // --- FORMULAIRE ---
    #[Route('/reservation', name: 'app_reservation_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        // On définit une référence par défaut pour éviter l'erreur SQL
        $reservation->setReference('RDV-' . uniqid());
        $reservation->setStatus('PENDING'); // Statut par défaut

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 1. Date et Heure
            $dateOnly = $reservation->getDateRdv();
            $timeStr = $form->get('timeSlot')->getData();
            if ($timeStr) {
                [$hours, $minutes] = explode(':', $timeStr);
                $dateOnly->setTime($hours, $minutes);
                $reservation->setDateRdv($dateOnly);
            }

            // 2. Calculs
            $totalPrice = 0;
            $totalDuration = 0;
            foreach ($reservation->getServices() as $service) {
                $totalPrice += $service->getPrice();
                $totalDuration += $service->getDuration();
            }

            // Option Dépose
            if ($form->get('hasRemoval')->getData()) {
                $totalPrice += 10;
                $totalDuration += 20;
                $currentComment = $reservation->getComment();
                $reservation->setComment($currentComment . " [OPTION DÉPOSE INCLUSE]");
            }
            $roundedDuration = (int) (ceil($totalDuration / 15) * 15);
            $reservation->setTotalPrice($totalPrice);
            $reservation->setTotalDuration($roundedDuration);

            // 3. Adresse
            $fullAddress = sprintf('%s à %s',
                $reservation->getVisitAddress(),
                $form->get('visitCity')->getData()
            );
            $reservation->setVisitAddress($fullAddress);

            // 4. Gestion Paiement (Adapté à votre entité)
            // Votre entité n'a pas setPaymentMethod, on utilise le statut
            $paymentMethod = $form->get('paymentMethod')->getData();
            if ($paymentMethod === 'online') {
                $reservation->setStatus('CONFIRMED');
            } else {
                $reservation->setStatus('PENDING_PAYMENT');
            }

            $entityManager->persist($reservation);
            $entityManager->flush();


            return $this->redirectToRoute('app_reservation_success', ['id' => $reservation->getId()]);
        }

        return $this->render('reservation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // --- AJAX DISPONIBILITÉS ---
    #[Route('/booking/available-slots', name: 'booking_available_slots', methods: ['GET'])]
    public function getAvailableSlots(Request $request, ReservationRepository $reservationRepo): JsonResponse
    {
        $dateString = $request->query->get('date');
        $durationMinutes = (int) $request->query->get('duration');

        if (!$dateString || $durationMinutes <= 0) {
            return new JsonResponse([], 400);
        }

        try {
            $date = new \DateTime($dateString);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Date invalide'], 400);
        }

        $dayOfWeek = (int) $date->format('w');
        if ($dayOfWeek === 0) return new JsonResponse([]); // Dimanche fermé

        if ($dayOfWeek === 6) {
            $openTime = (clone $date)->setTime(9, 0);
            $closeTime = (clone $date)->setTime(14, 0);
        } else {
            $openTime = (clone $date)->setTime(9, 0);
            $closeTime = (clone $date)->setTime(19, 0);
        }

        // Appel au Repository corrigé
        try {
            $reservations = $reservationRepo->findConfirmedByDate($date);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }

        $busyIntervals = [];
        // Pause déj
        $busyIntervals[] = [
            'start' => (clone $date)->setTime(12, 0)->getTimestamp(),
            'end' => (clone $date)->setTime(13, 0)->getTimestamp()
        ];

        foreach ($reservations as $resa) {
            // CORRECTION CRITIQUE : Utilisation des Getters exacts de votre Entité
            $dateRdv = $resa->getDateRdv(); // CamelCase généré par Symfony
            $duree = $resa->getTotalDuration(); // CamelCase généré par Symfony

            if ($dateRdv) {
                $start = $dateRdv->getTimestamp();
                $finalDuration = $duree ?: 60;
                $end = $start + ($finalDuration * 60) + (30 * 60); // +30min trajet
                $busyIntervals[] = ['start' => $start, 'end' => $end];
            }
        }

        $availableSlots = [];
        $cursor = $openTime->getTimestamp();
        $limitTimestamp = $closeTime->getTimestamp();

        while ($cursor + ($durationMinutes * 60) <= $limitTimestamp) {
            $slotStart = $cursor;
            $slotEnd = $cursor + ($durationMinutes * 60);
            $isFree = true;

            foreach ($busyIntervals as $busy) {
                if ($slotStart < $busy['end'] && $slotEnd > $busy['start']) {
                    $isFree = false;
                    break;
                }
            }

            if ($isFree) {
                $availableSlots[] = [
                    'time' => date('H:i', $slotStart),
                    'label' => date('H:i', $slotStart)
                ];
            }

            $cursor += 900;
        }

        return new JsonResponse($availableSlots);
    }

    #[Route('/reservation/success/{id}', name: 'app_reservation_success')]
    public function success(Reservation $reservation): Response
    {
        return $this->render('reservation/success.html.twig', [
            'reservation' => $reservation
        ]);
    }
}
