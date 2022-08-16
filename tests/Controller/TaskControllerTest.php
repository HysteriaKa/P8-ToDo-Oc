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
    private EntityManager $entityManager;
    public function setUp(): void
    {
        $this->client = static::createClient();
        
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }
    public function testAdmin(): void
    {

        $userRepository = $this->entityManager->getRepository(User::class);
        $taskRepository = $this->entityManager->getRepository(Task::class);
        $testUser = $userRepository->findOneByEmail('karine2310@gmail.com');
        $user = $userRepository->findOneByEmail('henriette.dumas@gmail.com');
        $task = $taskRepository->findOneBy(['user' => $user]);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('img.slide-image');
        // $this->assertCount(1, $crawler->filter('a[href="/tasks/' . $task->getId() . '/edit"]'));
    }

    public function testUser(): void
    {

        $userRepository = $this->entityManager->getRepository(User::class);
        $taskRepository = $this->entityManager->getRepository(Task::class);
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

    public function testAddTask()
    {

        $userRepository = $this->entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneByEmail('karine2310@gmail.com');
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Test tâche',
            'task[content]' => 'Description tâche test'
        ]);
        $this->client->submit($form);
        $taskRepository = $this->entityManager->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['title' => 'Test tâche']);
        $this->assertInstanceOf(Task::class,$task);
        $this->assertEquals($testUser->getId(),$task->getUser()->getId());
    }

    public function testEditTask(){
        $userRepository = $this->entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneByEmail('alphonse.richard@email.com');
        $taskRepository = $this->entityManager->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['user' =>$testUser]);
        
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/edit');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Test modif tâche',
            'task[content]' => 'Description tâche test modif'
        ]);
        $this->client->submit($form);
        $this->client->followRedirect();
        $task=$taskRepository->find($task->getId());
        $this->assertEquals('Test modif tâche',$task->getTitle());

    }

    public function testTaskEditTaskByAnother(){
        $userRepository = $this->entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneByEmail('alphonse.richard@email.com');
        $user = $userRepository->findOneByEmail('henriette.dumas@gmail.com');
        $taskRepository = $this->entityManager->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['user' =>$user]);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/edit');
        $this->assertResponseRedirects(('/redirect/NonAuthorised'));

    }
    public function testTaskEditTaskAdmin(){
        $userRepository = $this->entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneByEmail('karine2310@gmail.com');
        $user = $userRepository->findOneByEmail('henriette.dumas@gmail.com');
        
        $taskRepository = $this->entityManager->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['user' =>$user]);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/edit');
        $this->assertResponseIsSuccessful();

    }
    public function testDeleteTaskIsSuccessful(){
        $userRepository = $this->entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneByEmail('henriette.dumas@gmail.com');
        $taskRepository = $this->entityManager->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['user' =>$testUser]);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks/'.$task->getId().'/delete');
        // $this->assertResponseIsSuccessful();
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert-success', 'La tâche a bien été supprimée.');
        $this->assertRouteSame('task_list');

    }
    public function testDeleteTaskAnonymousIfAdmin(){
        $userRepository = $this->entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneByEmail('karine2310@gmail.com');
        $taskRepository = $this->entityManager->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['user' =>$userRepository->findOneBy(['username'=>'anonymous'])]);
        $this->client->loginUser($testUser);
        $this->client->request('GET', '/tasks/'.$task->getId().'/delete');
        // $this->assertResponseIsSuccessful();
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert-success', 'La tâche a bien été supprimée.');
        $this->assertRouteSame('task_list');

    }
    public function testDeleteTaskAnonymousIfNotAdmin(){
        $userRepository = $this->entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneByEmail('henriette.dumas@gmail.com');
        $taskRepository = $this->entityManager->getRepository(Task::class);
        $task = $taskRepository->findOneBy(['user' =>$userRepository->findOneBy(['username'=>'anonymous'])]);
        $this->client->loginUser($testUser);
        $this->client->request('GET', '/tasks/'.$task->getId().'/delete');
        // $this->assertResponseIsSuccessful();
        $this->assertResponseRedirects(('/redirect/NonAuthorised'));
        

    }
}
