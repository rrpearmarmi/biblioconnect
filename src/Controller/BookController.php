<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
    #[IsGranted('ROLE_USER')]
    public function catalog(Request $request, BookRepository $bookRepository): Response
    {
        $query = $request->query->get('q');
        
        if ($query) {
            $books = $bookRepository->createQueryBuilder('b')
                ->leftJoin('b.authors', 'a')
                ->leftJoin('b.categories', 'c')
                ->where('b.title LIKE :query')
                ->orWhere('a.name LIKE :query')
                ->orWhere('c.name LIKE :query')
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
    #[IsGranted('ROLE_USER')]
    public function detail(Book $book): Response
    {
        return $this->render('book/detail.html.twig', [
            'book' => $book,
        ]);
    }
}
