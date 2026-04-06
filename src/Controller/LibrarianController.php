<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/librarian')]
class LibrarianController extends AbstractController
{
    #[Route('', name: 'app_librarian_dashboard')]
    public function index(): Response
    {
        return $this->render('librarian/dashboard.html.twig', [
            'controller_name' => 'LibrarianController',
        ]);
    }

    #[Route('/catalog', name: 'app_librarian_catalog')]
    public function catalog(BookRepository $bookRepository): Response
    {
        return $this->render('librarian/catalog.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    #[Route('/reservations', name: 'app_librarian_reservations')]
    public function reservations(ReservationRepository $reservationRepository): Response
    {
        return $this->render('librarian/reservations.html.twig', [
            'reservations' => $reservationRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }
}
