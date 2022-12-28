<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
  private KernelBrowser $client;
  private EntityManagerInterface $em;
  private Router $urlGenerator;

  public function setUp(): void
  {
    $this->client = static::createClient();
    $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    $this->urlGenerator = $this->client->getContainer()->get('router.default');
  }

  public function testLoginUser()
  {
    //test si User logue OK
    $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'mirtille@pommemail.com']);
    $this->client->loginUser($user);

    $this->client->request('GET', $this->urlGenerator->generate('homepage'));
    $this->assertResponseIsSuccessful();
    $this->assertSelectorTextContains('#logout', 'Se déconnecter');
  }

  public function testListNotAllowAccessPageListUserAction()
  {
    $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'mirtille@pommemail.com']);
    $this->client->loginUser($user);

    $this->client->request('GET', $this->urlGenerator->generate('user_list'));
    $this->assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
  }

  public function testListAllowAccessPageListUserAction()
  {
    $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'kiwi@pommemail.com']);
    $this->client->loginUser($user);
    $crawler = $this->client->request('GET', $this->urlGenerator->generate('user_list'));
    $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
    $this->assertEquals(1, $crawler->filter('html:contains("mirtille@pommemail.com")')->count());
    $this->assertEquals(1, $crawler->filter('html:contains("cassis@pommemail.com")')->count());
    $this->assertEquals(1, $crawler->filter('html:contains("kiwi@pommemail.com")')->count());
  }

  public function testEditAction()
  {
    $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'cassis@pommemail.com']);
    $this->client->loginUser($user);
    $this->client->request('GET', $this->urlGenerator->generate('user_edit', ['id' => $user->getId()]));
    $this->client->submitForm('Modifier', [
      'user[username]' => 'alineUpdated',
      'user[password][first]' => 'aline',
      'user[password][second]' => 'aline',
      'user[email]' => 'cassis@pommemail.com'
    ]);

    $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'cassis@pommemail.com']);
    $this->client->loginUser($user);
    $this->client->request('GET', $this->urlGenerator->generate('homepage'));
    $this->assertSelectorTextSame('#hello-block>strong', $user->getUsername());
  }
}
