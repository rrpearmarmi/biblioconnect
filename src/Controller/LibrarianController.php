<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/librarian')]
class LibrarianController extends AbstractController
{
    #[Route('', name: 'app_librarian_dashboard')]
    public function index(BookRepository $bookRepository, ReservationRepository $reservationRepository): Response
    {
        return $this->render('librarian/dashboard.html.twig', [
            'books' => $bookRepository->findAll(),
            'reservations' => $reservationRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_librarian_book_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Logic for a new book: available copies = total copies at start
            $book->setAvailableCopies($book->getTotalCopies());
            
            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', 'Ouvrage ajouté au catalogue.');
            return $this->redirectToRoute('app_librarian_dashboard');
        }

        return $this->render('librarian/book_form.html.twig', [
            'form' => $form,
            'book' => $book,
            'title' => 'Ajouter un ouvrage'
        ]);
    }

    #[Route('/edit/{id}', name: 'app_librarian_book_edit')]
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Note: If totalCopies was changed, we might need complex re-calculation 
            // of availableCopies, but for this simple version we keep it manual.
            
            $entityManager->flush();
            $this->addFlash('success', 'Ouvrage mis à jour.');
            return $this->redirectToRoute('app_librarian_dashboard');
        }

        return $this->render('librarian/book_form.html.twig', [
            'form' => $form,
            'book' => $book,
            'title' => 'Modifier l\'ouvrage'
        ]);
    }
}
