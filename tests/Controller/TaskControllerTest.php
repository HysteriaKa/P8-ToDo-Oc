<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private EntityManager $em;
    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }
    public function testAdmin(): void
    {

        $userRepository = $this->em->getRepository(User::class);
        $taskRepository = $this->em->getRepository(Task::class);
        $testUser = $userRepository->findOneByEmail('karine2310@gmail.com');
        $user = $userRepository->findOneByEmail('henriette.dumas@gmail.com');
        $task = $taskRepository->findOneBy(['user' => $user]);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('img.slide-image');
        $this->assertCount(1, $crawler->filter('a[href="/tasks/' . $task->getId() . '/edit"]'));
    }
    
    public function testUser(): void
    {

        $userRepository = $this->em->getRepository(User::class);
        $taskRepository = $this->em->getRepository(Task::class);
        $testUser = $userRepository->findOneByEmail('alphonse.richard@email.com');
        $user = $userRepository->findOneByEmail('henriette.dumas@gmail.com');
        $task = $taskRepository->findOneBy(['user' => $user]);

        $this->client->loginUser($testUser);
        $crawler =  $this->client->request('GET', '/tasks');

        $this->assertCount(0, $crawler->filter('a[href="/tasks/' . $task->getId() . '/edit"]'));
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('img.slide-image');
        // $this->assertSelectorNotExists('a[href="/tasks/'.$task->getId().'/edit"]');

    }
}
