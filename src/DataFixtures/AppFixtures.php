<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Category;
use App\Entity\Favorite;
use App\Entity\Language;
use App\Entity\Reservation;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
       
        $languages = [];
        $langsData = [
            ['name' => 'Français', 'code' => 'fr'],
            ['name' => 'Anglais', 'code' => 'en'],
            ['name' => 'Arabe', 'code' => 'ar'],
        ];

        foreach ($langsData as $data) {
            $lang = new Language();
            $lang->setName($data['name']);
            $lang->setCode($data['code']);
            $manager->persist($lang);
            $languages[] = $lang;
        }

        $categories = [];
        $catData = [
            ['name' => 'Roman', 'slug' => 'roman'],
            ['name' => 'Science-Fiction', 'slug' => 'science-fiction'],
            ['name' => 'Histoire', 'slug' => 'histoire'],
            ['name' => 'Biographie', 'slug' => 'biographie'],
            ['name' => 'Développement Personnel', 'slug' => 'dev-perso'],
            ['name' => 'Informatique', 'slug' => 'informatique'],
        ];

        foreach ($catData as $data) {
            $cat = new Category();
            $cat->setName($data['name']);
            $cat->setSlug($data['slug']);
            $manager->persist($cat);
            $categories[] = $cat;
        }

        $authors = [];
        for ($i = 1; $i <= 10; $i++) {
            $author = new Author();
            $author->setName("Auteur Nom $i");
            $author->setBio("Biographie courte pour l'auteur $i. Un écrivain reconnu mondialement.");
            $manager->persist($author);
            $authors[] = $author;
        }

        $users = [];

        $admin = new User();
        $admin->setEmail('admin@biblioconnect.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $admin->setFirstName('Admin');
        $admin->setLastName('System');
        $manager->persist($admin);

        for ($i = 1; $i <= 2; $i++) {
            $lib = new User();
            $lib->setEmail("bibliothecaire$i@biblioconnect.fr");
            $lib->setRoles(['ROLE_LIBRARIAN']);
            $lib->setPassword($this->hasher->hashPassword($lib, 'lib123'));
            $lib->setFirstName('Biblio');
            $lib->setLastName("User$i");
            $manager->persist($lib);
        }

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail("user$i@gmail.com");
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->hasher->hashPassword($user, 'user123'));
            $user->setFirstName("Prénom$i");
            $user->setLastName("Nom$i");
            $manager->persist($user);
            $users[] = $user;
        }

        $books = [];
        for ($i = 1; $i <= 30; $i++) {
            $book = new Book();
            $book->setTitle("Livre Numéro $i");
            $book->setDescription("Une description fascinante pour le livre $i. Plongez dans une aventure incroyable et inoubliable.");
            $book->setLanguage($languages[array_rand($languages)]);
            $book->setPublishedYear(rand(1900, 2024));
            
            $copies = rand(1, 5);
            $book->setTotalCopies($copies);
            $book->setAvailableCopies($copies);

            $numCats = rand(1, 3);
            $shuffledCats = $categories;
            shuffle($shuffledCats);
            for ($c = 0; $c < $numCats; $c++) {
                $book->addCategory($shuffledCats[$c]);
            }

            $numAuthors = rand(1, 2);
            $shuffledAuthors = $authors;
            shuffle($shuffledAuthors);
            for ($a = 0; $a < $numAuthors; $a++) {
                $book->addAuthor($shuffledAuthors[$a]);
            }

            $manager->persist($book);
            $books[] = $book;
        }

        for ($i = 1; $i <= 20; $i++) {
            $res = new Reservation();
            $res->setUser($users[array_rand($users)]);
            
            $book = $books[array_rand($books)];
            $res->setBook($book);
            
            $statusChoice = rand(1, 10);
            if ($statusChoice <= 4) {
                    $res->setStatus('returned');
                $start = new \DateTime('-' . rand(20, 60) . ' days');
                $end = clone $start;
                $end->modify('+14 days');
                $returned = clone $end;
                $returned->modify('-' . rand(1, 5) . ' days');
                
                $res->setStartDate($start);
                $res->setEndDate($end);
                $res->setReturnedAt($returned);
            } elseif ($statusChoice <= 8) {
                $res->setStatus('active');
                $start = new \DateTime('-' . rand(1, 10) . ' days');
                $end = clone $start;
                $end->modify('+14 days');
                
                $res->setStartDate($start);
                $res->setEndDate($end);
                
                if ($book->getAvailableCopies() > 0) {
                    $book->setAvailableCopies($book->getAvailableCopies() - 1);
                }
            } else {
                $res->setStatus('pending');
                $start = new \DateTime('+' . rand(1, 5) . ' days');
                $end = clone $start;
                $end->modify('+14 days');
                
                $res->setStartDate($start);
                $res->setEndDate($end);
            }

            $manager->persist($res);
        }

        // 7. Create Reviews (Ensure unique user-book pairs using array indices)
        $reviewedPairs = [];
        for ($i = 1; $i <= 15; $i++) {
            $userIndex = array_rand($users);
            $bookIndex = array_rand($books);
            $pairKey = $userIndex . '-' . $bookIndex;

            if (in_array($pairKey, $reviewedPairs)) {
                $i--; // Retry
                continue;
            }

            $user = $users[$userIndex];
            $book = $books[$bookIndex];

            $review = new Review();
            $review->setUser($user);
            $review->setBook($book);
            $review->setRating(rand(3, 5));
            $review->setComment("Ce livre est vraiment génial ! Je le recommande vivement. (Commentaire $i)");
            $review->setIsModerated(true);
            $review->setIsVisible(true);
            $manager->persist($review);
            
            $reviewedPairs[] = $pairKey;
        }

        // 8. Create Favorites (Ensure unique user-book pairs using array indices)
        $favoritePairs = [];
        for ($i = 1; $i <= 10; $i++) {
            $userIndex = array_rand($users);
            $bookIndex = array_rand($books);
            $pairKey = $userIndex . '-' . $bookIndex;

            if (in_array($pairKey, $reviewedPairs) || in_array($pairKey, $favoritePairs)) {
                $i--; // Retry
                continue;
            }

            $user = $users[$userIndex];
            $book = $books[$bookIndex];

            $fav = new Favorite();
            $fav->setUser($user);
            $fav->setBook($book);
            $manager->persist($fav);
            
            $favoritePairs[] = $pairKey;
        }

        $manager->flush();
    }
}
