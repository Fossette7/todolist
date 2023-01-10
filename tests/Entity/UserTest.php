<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
  public function testUserUsername()
  {
    $user = new User();
    $username = "RobertDu92";

    $user->setUsername($username);
    $this->assertEquals($username, $user->getUsername());
  }

  public function testUserEmail()
  {
    $user = new User();
    $email = "bebert@gmail.com";
    $user->setEmail($email);
    $this->assertEquals($email, $user->getEmail());
  }

  public function testUserRole()
  {
    $user = new User();
    $roles = ['ROLE_USER', 'ROLE_USER', 'ROLE_ADMIN'];
    $user->setRoles($roles);
    $this->assertEquals(array_unique($roles), $user->getRoles());
  }

  public function testAddTasks()
  {
    $user = new User();
    $task = new Task();
    $task2 = new Task();

    $user->addTask($task);
    $this->assertCount(1, $user->getTasks());
    $user->addTask($task2);
    $this->assertCount(2, $user->getTasks());
  }

  public function testRemoveTasks()
  {
    $user = new User();
    $task = new Task();
    $task2 = new Task();

    $user->addTask($task);
    $user->addTask($task2);
    $this->assertCount(2, $user->getTasks());
    $user->removeTask($task);
    $this->assertCount(1, $user->getTasks());
    $user->removeTask($task2);
    $this->assertCount(0, $user->getTasks());
  }
}
