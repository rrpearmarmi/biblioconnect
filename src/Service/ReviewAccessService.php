<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\User;
use App\Repository\ReservationRepository;
use App\Repository\ReviewRepository;

class ReviewAccessService
{
    public function __construct(
        private ReservationRepository $reservationRepository,
        private ReviewRepository $reviewRepository
    ) {
    }

    public function canReview(User $user, Book $book): bool
    {
        // Check if user already reviewed the book
        $existingReview = $this->reviewRepository->findOneBy([
            'user' => $user,
            'book' => $book
        ]);

        if ($existingReview) {
            return false;
        }

        // Check if user has returned the book
        $reservations = $this->reservationRepository->findBy([
            'user' => $user,
            'book' => $book,
            'status' => 'returned'
        ]);

        return count($reservations) > 0;
    }
}
