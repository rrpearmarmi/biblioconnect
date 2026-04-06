<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(BookRepository $bookRepository): Response
    {
        // Simple featured books logic: get the latest 6 books
        $featuredBooks = $bookRepository->findBy([], ['id' => 'DESC'], 6);
        
        return $this->render('home/index.html.twig', [
            'featuredBooks' => $featuredBooks,
        ]);
    }

    #[Route('/books', name: 'app_book_catalog')]
    public function catalog(Request $request, BookRepository $bookRepository): Response
    {
        $query = $request->query->get('q');
        
        if ($query) {
            // Simple search by title for now. In a real app we would use QueryBuilder
            $books = $bookRepository->createQueryBuilder('b')
                ->where('b.title LIKE :query')
                ->setParameter('query', '%' . $query . '%')
                ->getQuery()
                ->getResult();
        } else {
            $books = $bookRepository->findAll();
        }

        return $this->render('book/catalog.html.twig', [
            'books' => $books,
            'query' => $query
        ]);
    }

    #[Route('/books/{id}', name: 'app_book_detail')]
    public function detail(Book $book): Response
    {
        return $this->render('book/detail.html.twig', [
            'book' => $book,
        ]);
    }
}
