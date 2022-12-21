<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture
{
    public const TASK_REFERENCE = 'task-';
    private $taskDatas = [
        [
            'content' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book",
            'title' => 'tache 1',
            'createdAt' => '12/31/2019',
            'toggle' => 1
        ],
        [
            'content' => "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters",
            'title' => 'tache 2',
            'createdAt' => '10/04/2021',
            'toggle' => 1
        ],
        [
            'content' => "Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney ",
            'title' => 'tache 3',
            'createdAt' => '01/21/2022',
            'toggle' => 0
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach ($this->taskDatas as $key => $taskData)
        {
            $task = new Task();
            $task->setContent($taskData['content']);
            $task->setTitle($taskData['title']);
            $task->setCreatedAt(new \DateTime($taskData['createdAt']));
            $task->toggle($taskData['toggle']);
            $this->setReference(self::TASK_REFERENCE.$key, $task);
            $manager->persist($task);
            $manager->flush();
        }
    }
}
