<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HTTPFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
  private $client;

  //HTTP client creation
  public function setUp(): void
  {
    $this->client = static::createClient();
    $this->userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
    $this->user = $this->userRepository->findOneByEmail('fraise@pommemail.fr');
    $this->urlGenerator = $this->client->getContainer()->get('router.default');
    $this->client->loginUser($this->user);
  }

  public function testLoginUser()
  {
    //test si User logue OK
    $crawler = $client->request('GET','/login');
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    $this->assertSelectorTextContains('div.alert.alert-success','L\'utilisateur a bien été ajoutée');

  }

  public function testListAction()
  {
    $this->loginUser();
    $this->client->request('GET','/users');
    $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
  }

}
