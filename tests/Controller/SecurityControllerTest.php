<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageDisplay(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Ravi de vous revoir');
        $this->assertSelectorExists('form[method="post"]');
    }

    public function testLoginSubmission(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'test@example.com',
            '_password' => 'password123',
        ]);

        $client->submit($form);
        $this->assertResponseRedirects(); // Should redirect on form submission
    }
}
