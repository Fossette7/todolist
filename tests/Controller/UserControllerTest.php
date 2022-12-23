<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
  private $client;
  private $em;
  private $urlGenerator;

  //HTTP client creation
  public function setUp(): void
  {
    $this->client = static::createClient();
    $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    $user = $this->em->getRepository(User::class)->findOneBy(['email'=> 'mirtille@pommemail.com']);
    $this->urlGenerator = $this->client->getContainer()->get('router.default');
    $this->client->loginUser($user);
  }

  public function testLoginUser()
  {
    //test si User logue OK
    $this->client->request('GET',$this->urlGenerator->generate('homepage'));
    $this->assertResponseIsSuccessful();
    $this->assertSelectorTextContains('#logout','Se déconnecter');
  }

  public function testListNotAllowAccessPageListUserAction()
  {
    $this->client->request('GET',$this->urlGenerator->generate('user_list'));
    $this->assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
  }

  public function testListAllowAccessPageListUserAction()
  {
    $user = $this->em->getRepository(User::class)->findOneBy(['email'=> 'kiwi@pommemail.com']);
    $this->client->loginUser($user);
    $crawler = $this->client->request('GET',$this->urlGenerator->generate('user_list'));
    $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
    $this->assertEquals(1, $crawler->filter('html:contains("mirtille@pommemail.com")')->count());
    $this->assertEquals(1, $crawler->filter('html:contains("cassis@pommemail.com")')->count());
    $this->assertEquals(1, $crawler->filter('html:contains("kiwi@pommemail.com")')->count());
  }

}
