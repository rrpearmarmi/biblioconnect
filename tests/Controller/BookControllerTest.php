<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    public function testCatalogPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/books');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Notre Catalogue');
        $this->assertSelectorExists('form[method="get"]'); // Search form
    }

    public function testSearchFunctionality(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/books?q=test');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.text-amber-800', 'Résultats pour : "test"');
    }
}
