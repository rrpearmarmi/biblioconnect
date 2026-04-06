<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomePageContent(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Découvrez un monde de savoirs sans limites');
        $this->assertSelectorExists('nav');
        $this->assertSelectorExists('footer');
    }

    public function testNavigationLinks(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertSelectorExists('a[href="/books"]');
        $this->assertSelectorExists('a[href="/login"]');
    }
}
