<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\ReservationRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin_dashboard')]
    public function index(
        BookRepository $bookRepository,
        UserRepository $userRepository,
        ReservationRepository $reservationRepository,
        ReviewRepository $reviewRepository
    ): Response {
        return $this->render('admin/dashboard.html.twig', [
            'total_books' => count($bookRepository->findAll()),
            'total_users' => count($userRepository->findAll()),
            'total_reservations' => count($reservationRepository->findAll()),
            'reviews_to_moderate' => $reviewRepository->findBy(['isModerated' => false]),
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/users', name: 'app_admin_users')]
    public function users(UserRepository $userRepository): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/reviews/moderation', name: 'app_admin_moderation')]
    public function moderation(ReviewRepository $reviewRepository): Response
    {
        return $this->render('admin/moderation.html.twig', [
            'reviews_to_moderate' => $reviewRepository->findBy(['isModerated' => false]),
        ]);
    }
}
