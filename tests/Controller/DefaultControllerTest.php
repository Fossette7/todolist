<?php

namespace App\Tests\Controller;

use App\Kernel;
use Symfony\Component\HTTPFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
  private KernelBrowser $client;

  public function setUp(): void
  {
    $this->client = static::createClient();
  }

  public function testDefaultIndex()
  {
    $this->client->request('GET','/');
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);
  }

}
