<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class TaskUnitTest extends TestCase
{
    public function testValidEntity(): void
    {
        $task = new Task();
        $user = new User();
        $createdAt = new DateTime();
        $user->setEmail('ka@test.fr');
        $task->setTitle('test task')
            ->setContent('still test task')
            ->setCreatedAt($createdAt)
            ->setDone(true)
            ->setUser($user);
        $this->assertEquals("test task", $task->getTitle());
        $this->assertEquals("still test task", $task->getContent());
        $this->assertEquals(true, $task->isDone());
        $this->assertEmpty($task->getId());
        $task->toggle(false);
        $this->assertEquals(false, $task->isDone());
        $this->assertEquals($createdAt, $task->getCreatedAt());
        $this->assertInstanceOf(User::class, $task->getUser());
        $this->assertEquals('ka@test.fr',$task->getUser()->getEmail());
        
    }
}
