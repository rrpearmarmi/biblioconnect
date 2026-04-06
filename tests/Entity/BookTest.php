<?php

namespace App\Tests\Entity;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Category;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    public function testBookProperties(): void
    {
        $book = new Book();
        $book->setTitle('L\'Alchimiste');
        $book->setDescription('Un conte philosophique sur la quête personnelle.');
        $book->setTotalCopies(5);
        $book->setAvailableCopies(3);
        $book->setPublishedYear(1988);

        $this->assertEquals('L\'Alchimiste', $book->getTitle());
        $this->assertEquals('Un conte philosophique sur la quête personnelle.', $book->getDescription());
        $this->assertEquals(5, $book->getTotalCopies());
        $this->assertEquals(3, $book->getAvailableCopies());
        $this->assertEquals(1988, $book->getPublishedYear());
    }

    public function testBookAssociations(): void
    {
        $book = new Book();
        $author = new Author();
        $author->setName('Paulo Coelho');
        $book->addAuthor($author);

        $category = new Category();
        $category->setName('Philosophie');
        $book->addCategory($category);

        $this->assertCount(1, $book->getAuthors());
        $this->assertContains($author, $book->getAuthors());
        $this->assertCount(1, $book->getCategories());
        $this->assertContains($category, $book->getCategories());
    }
}
