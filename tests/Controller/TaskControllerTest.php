<?php

namespace App\Tests\Controller;

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
  private KernelBrowser $client;

  //HTTP client creation
  public function setUp(): void
  {
    $this->client = static::createClient();
  }

  //display Tasks List Test
  public function testTasksList()
  {
    $this->client->request('GET','/');
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);
  }

  //display Task creation page
  public function testTaskCreatePage()
  {
    $this->client->request('GET','/tasks/create');
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);
  }

}
