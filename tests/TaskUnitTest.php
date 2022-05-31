<?php

namespace App\Tests;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskUnitTest extends TestCase
{
    public function testValidEntity(): void
    {
        $task = new Task();

        $task->setTitle('test task')
        ->setContent('still test task')
        ->setCreatedAt(new \DateTime())
        ->setDone(true);
        $this->assertEquals("test task", $task->getTitle());
        $this->assertEquals("still test task", $task->getContent());
        $this->assertEquals(true, $task->isDone());      

    }

}
