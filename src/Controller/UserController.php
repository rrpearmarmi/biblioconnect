<?php

namespace App\Controller;

use App\Repository\FavoriteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard')]
class UserController extends AbstractController
{
    #[Route('', name: 'app_user_dashboard')]
    public function index(FavoriteRepository $favoriteRepository): Response
    {
        $user = $this->getUser();
        
        return $this->render('user/dashboard.html.twig', [
            'user' => $user,
            'reservations' => $user->getReservations(),
            'favorites' => $favoriteRepository->findBy(['user' => $user]),
            'reviews' => $user->getReviews(),
        ]);
    }

    #[Route('/profile', name: 'app_user_profile')]
    public function profile(): Response
    {
        return $this->render('user/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
