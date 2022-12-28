<?php

namespace App\Tests\Form;

use App\Form\TaskType;
use App\Entity\Task;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{
  public function testSubmitCreateTaskValidData()
  {
    $titleTask = 'Create task on PHPunit';
    $contentTask = 'I m a task create by phpunit';

    $formData = [
      'title' => $titleTask,
      'content' => $contentTask,
    ];

    $currentTask = new Task();
    $form = $this->factory->create(TaskType::class, $currentTask);

    $expectedTask = new Task();
    $expectedTask->setCreatedAt($currentTask->getCreatedAt());
    $expectedTask->setTitle($formData['title']);
    $expectedTask->setContent($formData['content']);

    // submit the data to the form directly
    $form->submit($formData);

    // This check ensures there are no transformation failures
    $this->assertTrue($form->isSynchronized());

    // check that $formData was modified as expected when the form was submitted
    $this->assertEquals($expectedTask, $currentTask);
  }
}
