<?php

namespace App\Tests\Controller;

use App\Kernel;
use Symfony\Component\HTTPFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
  private KernelBrowser $client;

  //HTTP client creation
  public function setUp(): void
  {
    $this->client = static::createClient();
  }

}
