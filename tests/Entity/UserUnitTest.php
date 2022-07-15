<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class UserUnitTest extends TestCase
{
    public function testIsTrue(): void
    {
        $user = new User();
        $task = new Task();
        $task->setTitle('Testing');
        $user->setEmail("ka@test.fr")
            ->setUsername("ka@test.fr")
            ->setPassword('password')
            ->setCreatedAt(new \Datetime('now'));
        $user->addTask($task);
        $this->assertEquals("ka@test.fr", $user->getEmail());
        $this->assertEquals("ka@test.fr", $user->getUserIdentifier());
        $this->assertEquals("password", $user->getPassword());
        $this->assertEmpty($user->getId());
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertEquals("ka@test.fr", $user->getUsername());
        $this->assertEmpty($user->getSalt());
        $this->assertContains($task,$user->getTasks());
        $user->removeTask($task);
        $this->assertEmpty($user->getTasks());
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
