<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private EntityManager $entityManager;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->urlGenerator = $this->client->getContainer()->get('router');
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }



    public function testCreateUserNotAdmin()
    {

        $userRepository = $this->entityManager->getRepository(User::class);
        // $testUser = $userRepository->findOneByEmail('karine2310@gmail.com');
        //On connecte le user (admin)  
        // $this->client->loginUser($testUser);
        //on se rend sur la page de creation d un user
        $testUser = $userRepository->findOneByEmail('alphonse.richard@gmail.com');
        $this->client->loginUser($testUser);
        $this->client->request('GET', '/users/create');
        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateUser()
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneByEmail('karine2310@gmail.com');
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/users/create');
        $this->assertResponseIsSuccessful();
        $email= 'test'.uniqid().'@test.com';
        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'Tartenpion',
            'user[password][first]' => '123456',
            'user[password][second]' => '123456',
            'user[email]' => $email,
            'user[Roles]' => 'ROLE_USER'
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects();
        $crawler = $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $userCreated = $userRepository->findOneByEmail($email);
        $this->assertNotEmpty($userCreated);
        
    }
    
    public function testManageUsersNotAdmin()
    {

        $userRepository = $this->entityManager->getRepository(User::class);
        // $testUser = $userRepository->findOneByEmail('karine2310@gmail.com');
        //On connecte le user (admin)  
        // $this->client->loginUser($testUser);
        //on se rend sur la page de creation d un user
        $testUser = $userRepository->findOneByEmail('alphonse.richard@gmail.com');
        $this->client->loginUser($testUser);
        $this->client->request('GET', '/users');
        $this->assertResponseRedirects(('/redirect/NonAuthorised'));
    }
    public function testManageUsersAdmin()
    {

        $userRepository = $this->entityManager->getRepository(User::class);
        // $testUser = $userRepository->findOneByEmail('karine2310@gmail.com');
        //On connecte le user (admin)  
        // $this->client->loginUser($testUser);
        //on se rend sur la page de creation d un user
        $testUser = $userRepository->findOneByEmail('karine2310@gmail.com');
        $this->client->loginUser($testUser);
        $this->client->request('GET', '/users');
        $this->assertResponseIsSuccessful();
    }

}
