<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $user = new User();

        $user->setEmail("ka@test.fr")
            ->setUsername("ka@test.fr")
            ->setPassword('password')
            ->setCreatedAt(new \Datetime('now'));
        $this->assertEquals("ka@test.fr", $user->getEmail());
        $this->assertEquals("ka@test.fr", $user->getUserIdentifier());
        $this->assertEquals("password", $user->getPassword());
    }

    public function testIsEmpty(): void
    {
        $user = new User();
        $user->setPassword('');
        $this->assertEmpty($user->getEmail());
        $this->assertEmpty($user->getUserIdentifier());
        $this->assertEmpty($user->getPassword());
    }
}
