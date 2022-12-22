<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="task_list")
     */
    public function listAction(EntityManagerInterface $em)
    {
        return $this->render('task/list.html.twig', ['tasks' => $em->getRepository(Task::class)->findBy(['isDone' => 0])]);
    }

    /**
     * @Route("/tasks/done", name="task_list_done")
     */
    public function listTaskDone(EntityManagerInterface $em)
    {
      $taskDone = $em->getRepository(Task::class)->findBy(['isDone'=> 1]);

      return $this->render('task/list.html.twig', ['tasks' => $taskDone]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request, EntityManagerInterface $em)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $task->setAuthor($this->getUser());

            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function editAction(Task $task, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction(Task $task, EntityManagerInterface $em)
    {
        $task->toggle(!$task->isDone());
        $em->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     * @param EntityManagerInterface $em
     * @param Security $security
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteTaskAction(Task $task, EntityManagerInterface $em, \Symfony\Component\Security\Core\Security $security) :RedirectResponse
    {
      if (
            ($this->getUser() && $this->getUser() === $task->getAuthor()) ||
            ($task->getAuthor() === null && $security->isGranted('ROLE_ADMIN'))
          )
        {
          $em->remove($task);
          $em->flush();
          $this->addFlash('success', 'La tâche a bien été supprimée.');
        } else
        {
          throw new \Exception('Vous n\'avez pas les droits pour executer cette action');
        }

        return $this->redirectToRoute('task_list');
    }
}
