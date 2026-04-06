<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BookRepository;
use App\Repository\ReservationRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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
        $books = $bookRepository->findAll();
        $reservations = $reservationRepository->findBy([], ['createdAt' => 'DESC']);
        $pendingReservations = $reservationRepository->findBy(['status' => 'pending'], ['createdAt' => 'DESC']);
        $users = $userRepository->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'total_books' => count($books),
            'total_users' => count($users),
            'total_reservations' => count($reservations),
            'reviews_to_moderate' => $reviewRepository->findBy(['isModerated' => false]),
            'users' => $users,
            'books' => $books,
            'reservations' => $reservations,
            'pending_reservations' => $pendingReservations,
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

    #[Route('/user/toggle-role/{id}', name: 'app_admin_user_toggle_role')]
    public function toggleRole(User $user, EntityManagerInterface $entityManager): Response
    {
        $roles = $user->getRoles();
        if (in_array('ROLE_LIBRARIAN', $roles)) {
            $user->setRoles(['ROLE_USER']);
            $this->addFlash('success', 'Rôle Bibliothécaire retiré.');
        } else {
            $user->setRoles(['ROLE_USER', 'ROLE_LIBRARIAN']);
            $this->addFlash('success', 'Rôle Bibliothécaire ajouté.');
        }
        
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/user/ban/{id}', name: 'app_admin_user_ban')]
    public function banUser(User $user, EntityManagerInterface $entityManager): Response
    {
        // Simple "ban" logic: add a specific role or flag
        $user->setRoles(['ROLE_BANNED']);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur banni.');
        return $this->redirectToRoute('app_admin_dashboard');
    }
}
