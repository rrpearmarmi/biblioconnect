<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Reservation;
use App\Service\ReservationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reservations')]
#[IsGranted('ROLE_USER')]
class ReservationController extends AbstractController
{
    #[Route('/create/{id}', name: 'app_reservation_create', methods: ['POST'])]
    public function create(Request $request, Book $book, ReservationService $reservationService): Response
    {
        $startDate = $request->request->get('start_date');
        $endDate = $request->request->get('end_date');

        if (!$startDate || !$endDate) {
            $this->addFlash('error', 'Veuillez saisir les dates de début et de fin.');
            return $this->redirectToRoute('app_book_detail', ['id' => $book->getId()]);
        }

        $res = $reservationService->createReservation(
            $this->getUser(),
            $book,
            new \DateTime($startDate),
            new \DateTime($endDate)
        );

        if ($res) {
            $this->addFlash('success', 'Réservation effectuée avec succès.');
        } else {
            $this->addFlash('error', 'Livre indisponible pour ces dates.');
        }

        return $this->redirectToRoute('app_user_dashboard');
    }

    #[Route('/cancel/{id}', name: 'app_reservation_cancel', methods: ['POST'])]
    public function cancel(Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($reservation->getUser() !== $this->getUser()) {
             throw $this->createAccessDeniedException();
        }

        if ($reservation->getStatus() === 'pending') {
            $reservation->setStatus('cancelled');
            $book = $reservation->getBook();
            $book->setAvailableCopies($book->getAvailableCopies() + 1);
            $entityManager->flush();
            $this->addFlash('success', 'Réservation annulée.');
        }

        return $this->redirectToRoute('app_user_dashboard');
    }
}
