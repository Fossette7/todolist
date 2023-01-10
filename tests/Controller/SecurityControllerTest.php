<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
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

  public function testInvalidLoginAction()
  {
    $crawler = $this->client->request('GET', $this->urlGenerator->generate('login'));

    $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    $form = $crawler->selectButton("Se connecter")->form([
      "_username" => "toto",
      "_password" => 'tata'
    ]);

    $this->client->submit($form);
    $crawler = $this->client->followRedirect();
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    $this->assertEquals(1, $crawler->filter('.alert.alert-danger')->count());
    $this->assertSelectorTextSame('.alert.alert-danger', 'Invalid credentials.');
  }

  public function testValidLoginAction()
  {
    $crawler = $this->client->request('GET', $this->urlGenerator->generate('login'));

    $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    $form = $crawler->selectButton("Se connecter")->form([
      "_username" => "toto",
      "_password" => 'toto'
    ]);

    $this->client->submit($form);
    $crawler = $this->client->followRedirect();
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    $this->assertEquals(0, $crawler->filter('.alert.alert-danger')->count());
    $this->assertSelectorTextSame('#logout','Se déconnecter');
  }

}
