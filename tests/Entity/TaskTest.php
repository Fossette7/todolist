<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{

  public function testTaskId()
  {
    $task = new Task();
    $id = 1;

    $task->setId($id);
    $this->assertIsInt($id, $task->getId());
  }

  public function testTaskCreatedDate()
    {
      $task = new Task();
      $createdAt = new \DateTime();
      $task->setCreatedAt($createdAt);

      $this->assertEquals($createdAt, $task->getCreatedAt());
    }

    public function testTaskTitle()
      {
        $task = new Task();
        $title = " Je suis un titre 1 ";

        $task->setTitle($title);
        $this->assertEquals($title, $task->getTitle());
      }

    public function testTaskContent()
      {
        $task = new Task();
        $content = " Je suis un contenu 1 ";
        $task->setContent($content);
        $this->assertEquals($content, $task->getContent());
      }

    public function testTaskIsDone()
      {
        $task = new Task();
        $isDone = false;
        $task->setIsDone($isDone);
        $this->assertIsBool($isDone, $task->getIsDone());
      }

}
