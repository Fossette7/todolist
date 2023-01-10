<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
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

  public function testNotLoggedUserDisplayListAction()
  {
    $crawler = $this->client->request('GET', $this->urlGenerator->generate('task_list'));

    // Check http response code
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    // Check undo task display (thumbnail element)
    $taskUndo = $this->em->getRepository(Task::class)->findBy(['isDone' => 0]);
    $this->assertEquals(count($taskUndo), $crawler->filter('.thumbnail')->count());

    // Check create task button not available
    $this->assertEquals(0, $crawler->filter('.create-task')->count());

    // Check login link if not logged
    $this->assertSelectorTextSame('a:nth-child(2)', 'Se connecter');
  }

  public function testValidLoggedWithUserRoleDisplayListAction()
  {
    $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'mirtille@pommemail.com']);
    $this->client->loginUser($user);

    $crawler = $this->client->request('GET', $this->urlGenerator->generate('task_list'));

    // Check http response code
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    // Check undo task display (thumbnail element)
    $taskUndo = $this->em->getRepository(Task::class)->findBy(['isDone' => 0]);
    $this->assertEquals(count($taskUndo), $crawler->filter('.thumbnail')->count());

    // Check create task button available
    $this->assertEquals(1, $crawler->filter('.create-task')->count());

    // Check only toggle button available (Marquer comme faite)
    $this->assertEquals(1, $crawler->filter('.task:first-child > .thumbnail > .action-task > div')->count());
    $this->assertSelectorTextSame('.task:first-child > .thumbnail > .action-task', 'Marquer comme faite');

    // Check logout button
    $this->assertSelectorTextSame('#logout', 'Se déconnecter');
  }

  public function testValidLoggedWithAdminRoleDisplayListAction()
  {
    // Check access to Task list with Admin user logged
    $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'kiwi@pommemail.com']);
    $this->client->loginUser($user);

    $crawler = $this->client->request('GET', $this->urlGenerator->generate('task_list'));

    // Check http response code
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    // Check undo task undo display (thumbnail element)
    $taskUndo = $this->em->getRepository(Task::class)->findBy(['isDone' => 0]);
    $this->assertEquals(count($taskUndo), $crawler->filter('.thumbnail')->count());

    // Check create task button available
    $this->assertEquals(1, $crawler->filter('.create-task')->count());

    // Check if 2 buttons available (Marquer comme faite + Supprimer)
    $this->assertEquals(2, $crawler->filter('.task:first-child > .thumbnail > .action-task > div')->count());
    $this->assertSelectorTextSame('.task:first-child > .thumbnail > .action-task > div:first-child', 'Marquer comme faite');
    $this->assertSelectorTextSame('.task:first-child > .thumbnail > .action-task > div:nth-child(2)', 'Supprimer');

    // Check logout button
    $this->assertSelectorTextSame('#logout', 'Se déconnecter');
  }

  public function testRemoveDeleteAndIsDoneActionButtonOnTaskIfNoRoleAdmin()
  {
    // Login with Admin user
    $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'mirtille@pommemail.com']);
    $this->client->loginUser($user);

    $crawler = $this->client->request('GET', $this->urlGenerator->generate('task_list'));

    // Check http response code
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    // Check undo task display (thumbnail element)
    $taskUndo = $this->em->getRepository(Task::class)->findBy(['isDone' => 0]);
    $this->assertEquals(count($taskUndo), $crawler->filter('.thumbnail')->count());

    // Check create task button available
    $this->assertEquals(1, $crawler->filter('.create-task')->count());

    // Check if 2 buttons available (Marquer comme faite + Supprimer) if task.author === user
    $this->assertEquals(2, $crawler->filter('.task:nth-child(2) > .thumbnail > .action-task > div')->count());
    $this->assertSelectorTextSame('.task:nth-child(2) > .thumbnail > .action-task > div:first-child', 'Marquer comme faite');
    $this->assertSelectorTextSame('.task:nth-child(2) > .thumbnail > .action-task > div:nth-child(2)', 'Supprimer');
    $this->assertSelectorTextSame('.task:nth-child(2) > .thumbnail > .caption > .badge', $user->getUsername());

    // Check if only 1 button available (Marquer comme faite) if task.author !== user
    $this->assertEquals(1, $crawler->filter('.task:nth-child(1) > .thumbnail > .action-task > div')->count());
    $this->assertSelectorTextSame('.task:nth-child(1) > .thumbnail > .action-task > div:first-child', 'Marquer comme faite');
    $this->assertSelectorNotExists('.task:nth-child(1) > .thumbnail > .action-task > div:nth-child(2)');
    $this->assertNotEquals($crawler->filter('.task:nth-child(1) > .thumbnail > .caption > .badge')->text(), $user->getUsername());

    // Check logout button
    $this->assertSelectorTextSame('#logout', 'Se déconnecter');
  }


  public function testAccessWithAnonymousUserListTaskDoneAction()
  {
    // Login with Admin user
    $crawler = $this->client->request('GET', $this->urlGenerator->generate('task_list_done'));

    // Check http response code
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    // Check undo task display (thumbnail element)
    $taskUndo = $this->em->getRepository(Task::class)->findBy(['isDone' => 1]);
    $this->assertEquals(count($taskUndo), $crawler->filter('.thumbnail')->count());

    // Check create task button not available
    $this->assertEquals(0, $crawler->filter('.create-task')->count());

    // Check if 2 button available (Marquer comme faite + Supprimer) if task.author === user
    $this->assertEquals(1, $crawler->filter('.task:first-child > .thumbnail > .action-task > div')->count());
    $this->assertSelectorTextNotContains('.task:first-child > .thumbnail > .action-task > div:first-child', 'Marquer non terminée');
    $this->assertSelectorTextSame('.task:first-child > .thumbnail > .caption > .badge', 'Anonyme');

    // Check logout button
    $this->assertSelectorTextSame('a:nth-child(2)', 'Se connecter');
  }

  public function testAccessWithUserAuthorListTaskDoneAction()
  {
    // Login with author user
    $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'mirtille@pommemail.com']);
    $this->client->loginUser($user);

    // Login with Admin user
    $crawler = $this->client->request('GET', $this->urlGenerator->generate('task_list_done'));

    // Check http response code
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    // Check undo task display (thumbnail element)
    $taskUndo = $this->em->getRepository(Task::class)->findBy(['isDone' => 1]);
    $this->assertEquals(count($taskUndo), $crawler->filter('.thumbnail')->count());

    // Check create task button not available
    $this->assertEquals(1, $crawler->filter('.create-task')->count());

    // Check if 2 button available (Marquer comme faite + Supprimer) if task.author === user
    $this->assertEquals(1, $crawler->filter('.task:first-child > .thumbnail > .action-task > div')->count());
    $this->assertSelectorTextSame('.task:first-child > .thumbnail > .action-task > div:first-child', 'Marquer non terminée');
    $this->assertSelectorTextSame('.task:first-child > .thumbnail > .caption > .badge', 'Anonyme');

    // Check if 2 button available (Marquer comme faite + Supprimer) if task.author === user
    $this->assertEquals(2, $crawler->filter('.task:nth-child(2) > .thumbnail > .action-task > div')->count());
    $this->assertSelectorTextSame('.task:nth-child(2) > .thumbnail > .action-task > div:first-child', 'Marquer non terminée');
    $this->assertSelectorTextSame('.task:nth-child(2) > .thumbnail > .action-task > div:nth-child(2)', 'Supprimer');
    $this->assertSelectorTextSame('.task:nth-child(2) > .thumbnail > .caption > .badge', $user->getUsername());
  }

  public function testAccessWithUserWithAdminRoleListTaskDoneAction()
  {
    // Login with author user
    $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'kiwi@pommemail.com']);
    $this->client->loginUser($user);

    // Login with Admin user
    $crawler = $this->client->request('GET', $this->urlGenerator->generate('task_list_done'));

    // Check http response code
    $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    // Check undo task display (thumbnail element)
    $taskUndo = $this->em->getRepository(Task::class)->findBy(['isDone' => 1]);
    $this->assertEquals(count($taskUndo), $crawler->filter('.thumbnail')->count());

    // Check create task button not available
    $this->assertEquals(1, $crawler->filter('.create-task')->count());

    // Check if 2 button available (Marquer comme faite + Supprimer) if task.author === user
    $this->assertEquals(2, $crawler->filter('.task:first-child > .thumbnail > .action-task > div')->count());
    $this->assertSelectorTextSame('.task:first-child > .thumbnail > .action-task > div:first-child', 'Marquer non terminée');
    $this->assertSelectorTextSame('.task:first-child > .thumbnail > .action-task > div:nth-child(2)', 'Supprimer');
    $this->assertSelectorTextSame('.task:first-child > .thumbnail > .caption > .badge', 'Anonyme');

    // Check if 2 button available (Marquer comme faite + Supprimer) if task.author === user
    $this->assertEquals(1, $crawler->filter('.task:nth-child(2) > .thumbnail > .action-task > div')->count());
    $this->assertSelectorTextSame('.task:nth-child(2) > .thumbnail > .action-task > div:first-child', 'Marquer non terminée');
  }

  public function testCantDeleteTaskAction()
  {
    // Remove first task
    $firstTask = $this->em->getRepository(Task::class)->findOneBy([]);
    $this->client->request('GET', $this->urlGenerator->generate('task_delete', ['id' => $firstTask->getId()]));

    // Check if redirect on task list page
    $this->assertResponseRedirects($this->urlGenerator->generate('task_list'));

    // Apply redirect to continue and load task_list page on $this->client
    $crawler = $this->client->followRedirect();

    // Check delete message not set
    $this->assertEquals(0, $crawler->filter('.alert.alert-success')->count());
  }

  public function testAnonymousTaskDeleteWithAdminAccountAction()
  {
    // Connect with admin user
    $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'kiwi@pommemail.com']);
    $this->client->loginUser($user);

    // Remove first task
    $firstTask = $this->em->getRepository(Task::class)->findOneBy([]);
    $this->client->request('GET', $this->urlGenerator->generate('task_delete', ['id' => $firstTask->getId()]));

    // Check if redirect on task list page
    $this->assertResponseRedirects($this->urlGenerator->generate('task_list'));

    // Apply redirect to continue and load task_list page on $this->client
    $crawler = $this->client->followRedirect();

    // Check valid remove message display
    $this->assertEquals(1, $crawler->filter('.alert.alert-success')->count());
    $this->assertSelectorTextSame('.alert.alert-success', 'Superbe ! La tâche a bien été supprimée.');
  }

  public function testEditTaskAction()
  {
    $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'mirtille@pommemail.com']);
    $task = $this->em->getRepository(Task::class)->findOneBy(['title' => 'Final fantasy 7']);
    $this->client->loginUser($user);
    $this->client->request('GET', $this->urlGenerator->generate('task_edit', ['id' => $task->getId()]));
    $crawler = $this->client->submitForm('Modifier', [
      'task[title]' => 'Final fantasy 10',
      'task[content]' => 'Final Fantasy VII (ファイナルファンタジーVII, Fainaru Fantajī Sebun?) est un jeu vidéo de rôle développé par Square (devenu depuis Square Enix) sous la direction de Yoshinori Kitase et sorti en 1997',
    ]);

    $crawler = $this->client->followRedirect();
    $this->assertEquals(1, $crawler->filter('.thumbnail:contains("Final fantasy 10")')->count());
  }

  public function testCreateTaskAction()
  {
    $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'kiwi@pommemail.com']);
    $this->client->loginUser($user);
    $this->client->request('GET', '/tasks/create');
    $crawler = $this->client->submitForm('Ajouter', [
      'task[title]' => 'Data test',
      'task[content]' => ' Content data test, Content data test , Content data test, Content data test,Content data test',
    ]);

    $crawler = $this->client->followRedirect();
    $this->assertSelectorTextContains('div.alert.alert-success','La tâche a été bien été ajoutée.');
  }
}
