<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Favorite;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/favorites')]
#[IsGranted('ROLE_USER')]
class FavoriteController extends AbstractController
{
    #[Route('/toggle/{id}', name: 'app_favorite_toggle', methods: ['POST'])]
    public function toggle(Book $book, FavoriteRepository $favoriteRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $favorite = $favoriteRepository->findOneBy(['user' => $user, 'book' => $book]);

        if ($favorite) {
            $entityManager->remove($favorite);
            $this->addFlash('success', 'Retiré des favoris.');
        } else {
            $favorite = new Favorite();
            $favorite->setUser($user);
            $favorite->setBook($book);
            $entityManager->persist($favorite);
            $this->addFlash('success', 'Ajouté aux favoris.');
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_book_detail', ['id' => $book->getId()]);
    }
}
