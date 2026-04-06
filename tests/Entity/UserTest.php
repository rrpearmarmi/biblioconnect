<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserCreationAndRoles(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setFirstName('Jean');
        $user->setLastName('Dupont');
        $user->setRoles(['ROLE_ADMIN']);

        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('Jean', $user->getFirstName());
        $this->assertEquals('Dupont', $user->getLastName());
        
        // Roles should always include ROLE_USER even if not explicitly set
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        
        $this->assertEquals('test@example.com', $user->getUserIdentifier());
    }
}
