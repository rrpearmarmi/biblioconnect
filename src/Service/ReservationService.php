<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ReservationService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function canReserve(Book $book): bool
    {
        return $book->getAvailableCopies() > 0;
    }

    public function createReservation(User $user, Book $book, \DateTimeInterface $startDate, \DateTimeInterface $endDate): ?Reservation
    {
        if (!$this->canReserve($book)) {
            return null;
        }

        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setBook($book);
        $reservation->setStartDate($startDate);
        $reservation->setEndDate($endDate);
        $reservation->setStatus('pending');

        $book->setAvailableCopies($book->getAvailableCopies() - 1);

        $this->entityManager->persist($reservation);
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $reservation;
    }
    
    public function returnBook(Reservation $reservation): void
    {
        if ($reservation->getStatus() !== 'returned' && $reservation->getStatus() !== 'cancelled') {
            $reservation->setStatus('returned');
            $reservation->setReturnedAt(new \DateTimeImmutable());
            
            $book = $reservation->getBook();
            $book->setAvailableCopies($book->getAvailableCopies() + 1);
            
            $this->entityManager->flush();
        }
    }
}
