<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testHomeIsUp()
    {
        
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        
        $testUser = $userRepository->findOneByEmail('karine2310@gmail.com');
        $urlGenerator = $this->client->getContainer()->get('router.default');
        $this->client->loginUser($testUser);
        $this->client->request(Request::METHOD_GET, $urlGenerator->generate('app_home'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
