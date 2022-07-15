<?php

namespace App\Tests\Controller;

use App\Entity\User;

use Symfony\Component\HTTPFoundation\Request;
use Symfony\Component\HTTPFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;



class MainControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository =$this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $this->user =$this->userRepository->findOneByEmail('karine2310@gmail.com');
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
        $this->client->loginUser($this->user);
    }

    public function testHomeIsUp()
    {
        $this->client->request('GET', $this->urlGenerator->generate('app_home'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1','Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !');
    }

}
