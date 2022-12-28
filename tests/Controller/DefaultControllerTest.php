<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HTTPFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
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

  public function testValidNotLoggedDefaultIndex()
  {
    $crawler = $this->client->request('GET', $this->urlGenerator->generate('homepage'));

    // Check http response code
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    // Check welcome message
    $this->assertSelectorTextSame('h1', 'Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !');

    // Check number link on homepage
    $this->assertEquals(5, $crawler->filter('a')->count());

    // Check login link if not logged
    $this->assertSelectorTextSame('a:nth-child(2)', 'Se connecter');

    $this->assertSelectorNotExists('#logout');

    // Check if button créer une nouvelle tâche not here
    $this->assertSelectorTextNotContains('#task-action-container > a:nth-child(1)', 'Créer une nouvelle tâche');

    // Check if first button equals to Consulter la liste des tâches à faire
    $this->assertSelectorTextSame('#task-action-container > a:nth-child(1)', 'Consulter la liste des tâches à faire');
  }

  public function testValidLoggedDefaultIndex()
  {
    $user = $this->em->getRepository(User::class)->findOneBy(['email'=> 'mirtille@pommemail.com']);
    $this->client->loginUser($user);
    $crawler = $this->client->request('GET', $this->urlGenerator->generate('homepage'));

    // Check http response code
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    // Check welcome message
    $this->assertSelectorTextSame('h1', 'Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !');

    // Check number link on homepage
    $this->assertEquals(6, $crawler->filter('a')->count());

    // Check profil link if logged
    $this->assertSelectorTextSame('#login-container > div > a:nth-child(2)', 'Profil');

    // Check logout button
    $this->assertSelectorTextSame('#logout','Se déconnecter');

    // Check if button créer une nouvelle tâche here
    $this->assertSelectorTextSame('#task-action-container > a:nth-child(1)', 'Créer une nouvelle tâche');
  }
}
