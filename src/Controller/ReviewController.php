<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Review;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reviews')]
class ReviewController extends AbstractController
{
    #[Route('/add/{id}', name: 'app_review_add', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function add(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $rating = $request->request->get('rating');
        $comment = $request->request->get('comment');

        if (!$rating || $rating < 1 || $rating > 5) {
            $this->addFlash('error', 'Note invalide (1-5 étoiles).');
            return $this->redirectToRoute('app_book_detail', ['id' => $book->getId()]);
        }

        $review = new Review();
        $review->setUser($this->getUser());
        $review->setBook($book);
        $review->setRating((int)$rating);
        $review->setComment($comment);
        
        // Default to not moderated (or auto-show depending on policy)
        $review->setIsVisible(true); 

        $entityManager->persist($review);
        $entityManager->flush();

        $this->addFlash('success', 'Votre avis a été publié.');
        return $this->redirectToRoute('app_book_detail', ['id' => $book->getId()]);
    }

    #[Route('/admin/moderate/{id}/{action}', name: 'app_review_moderate')]
    #[IsGranted('ROLE_ADMIN')]
    public function moderate(Review $review, string $action, EntityManagerInterface $entityManager): Response
    {
        if ($action === 'hide') {
            $review->setIsVisible(false);
            $this->addFlash('success', 'Commentaire masqué.');
        } elseif ($action === 'show') {
            $review->setIsVisible(true);
            $this->addFlash('success', 'Commentaire visible.');
        }
        
        $review->setIsModerated(true);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_dashboard');
    }
}
