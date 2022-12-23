<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
  private KernelBrowser $client;
  private EntityManagerInterface $em;

  public function setUp(): void
  {
    $this->client = static::createClient();
    $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    $this->urlGenerator = $this->client->getContainer()->get('router.default');
  }

  public function testCantRemoveTodolistRemove()
  {
    $this->client->request('GET', $this->urlGenerator->generate('task_delete', ['id' => 1]));
    $this->assertResponseRedirects();
    $crawler = $this->client->followRedirect();
    $this->assertEquals(0, $crawler->filter('html:contains("La tâche a bien été supprimée.")')->count());
  }

  public function testValidAnonymousTodolistRemoveWithAdminAccount()
  {
    $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'kiwi@pommemail.com']);
    $this->client->loginUser($user);
    $this->client->request('GET', $this->urlGenerator->generate('task_delete', ['id' => 1]));
    $this->assertResponseRedirects();
    $crawler = $this->client->followRedirect();
    $this->assertEquals(1, $crawler->filter('html:contains("La tâche a bien été supprimée.")')->count());
  }
}
