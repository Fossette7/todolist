<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
  public const TASK_REFERENCE = 'task-';
  private $taskDatas = [
    [
      'content' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book",
      'title' => 'tache 1',
      'createdAt' => '12/12/2019',
      'toggle' => 1
    ],
    [
      'content' => "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters",
      'title' => 'tache 2',
      'createdAt' => '10/04/2021',
      'toggle' => 1,
      'userRefId' => 0
    ],
    [
      'content' => "Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney ",
      'title' => 'tache 3',
      'createdAt' => '01/21/2022',
      'toggle' => 0
    ],
    [
      'content' => "Final Fantasy VII (ファイナルファンタジーVII, Fainaru Fantajī Sebun?) est un jeu vidéo de rôle développé par Square (devenu depuis Square Enix) sous la direction de Yoshinori Kitase et sorti en 1997",
      'title' => 'Final fantasy 7',
      'createdAt' => '09/09/2022',
      'toggle' => 0,
      'userRefId' => 0
    ],
    [
      'content' => "Resident Evil, connue au Japon sous le nom Biohazard, est une série de jeux vidéo d'aventure, action et réflexion de type survival horror",
      'title' => 'Resident Evil',
      'createdAt' => '09/19/2020',
      'toggle' => 0,
      'userRefId' => 2
    ],
    [
      'content' => "Nicky Larson (シティーハンター CITY HUNTER, Shitī Hantā - City Hunter?)2 est l'adaptation, en anime, du manga japonais City Hunter de Tsukasa Hōjō, publié dans le Weekly Shōnen Jump à partir de 1985",
      'title' => 'Nicky Larson',
      'createdAt' => '03/01/2002',
      'toggle' => 0
    ],
  ];

  public function load(ObjectManager $manager): void
  {
    foreach ($this->taskDatas as $key => $taskData) {
      $task = new Task();
      $task->setContent($taskData['content']);
      $task->setTitle($taskData['title']);
      $task->setCreatedAt(new \DateTime($taskData['createdAt']));
      $task->toggle($taskData['toggle']);
      // Check if user to assign
      if (array_key_exists('userRefId', $taskData)) {
        $task->setAuthor($this->getReference(UserFixtures::USER_REFERENCE.$taskData['userRefId']));
      }
      $this->setReference(self::TASK_REFERENCE . $key, $task);
      $manager->persist($task);
      $manager->flush();
    }
  }

  public function getDependencies()
  {
    return [
      UserFixtures::class,
    ];
  }
}
