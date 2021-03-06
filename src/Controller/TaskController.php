<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{

    private $doctrine;
    private $em;

    public function __construct(EntityManagerInterface $em, ManagerRegistry $doctrine)

    {
        $this->em = $em;
        $this->doctrine = $doctrine;
    }


    /**
     * @Route("/tasks", name="task_list")
     */
    public function listActionAll()
    {
        $user = $this->getUser();
        if (!empty($user)) {

            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                $tasks = $this->doctrine->getRepository(Task::class)->findAll();
            } else {
                $tasks = $this->doctrine->getRepository(Task::class)->findBy(['user' => $user]);
            }
        }

        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }
    /**
     * @Route("/tasks/todo", name="task_list_todo")
     */
    public function listActionTodo()
    {
        $user = $this->getUser();
        if (!empty($user)) {

            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                $tasks = $this->doctrine->getRepository(Task::class)->findBy(['isDone' => 0]);
            } else {
                $tasks = $this->doctrine->getRepository(Task::class)->findBy(['user' => $user, 'isDone' => 0]);
            }
        }

        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }
    /**
     * @Route("/tasks/done", name="task_list_done")
     */
    public function listActionDone()
    {
        $user = $this->getUser();
        if (!empty($user)) {

            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                $tasks = $this->doctrine->getRepository(Task::class)->findBy(['isDone' => 1]);
            } else {
                $tasks = $this->doctrine->getRepository(Task::class)->findBy(['user' => $user, 'isDone' => 0]);
            }
        }

        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }
    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request)
    {
        $task = new Task();
        $user = $this->getUser();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUser($user);
            $task->setDone(false);
            $this->em->persist($task);
            $this->em->flush();

            $this->addFlash('success', 'La t??che a ??t?? bien ??t?? ajout??e.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function editAction(Task $task, Request $request)
    {
        $user = $this->getUser();
        if ($user->getId() !== $task->getUser()->getId() && !in_array('ROLE_ADMIN', $user->getRoles())) {

            return $this->redirectToRoute('redirect_nonAuthorised');
        }
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', 'La t??che a bien ??t?? modifi??e.');

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
    public function toggleTaskAction(Task $task)
    {
        $task->toggle(!$task->isDone());
        $this->em->flush();

        $this->addFlash('success', sprintf('La t??che %s a bien ??t?? marqu??e comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTaskAction(Task $task)
    {

        if (
            !in_array('ROLE_ADMIN', $this->getUser()->getRoles())
            && $task->getUser()->getId() !== $this->getUser()->getId()
        ) {
            return $this->redirectToRoute('redirect_nonAuthorised');
        }

        $this->em->remove($task);
        $this->em->flush();

        $this->addFlash('success', 'La t??che a bien ??t?? supprim??e.');

        return $this->redirectToRoute('task_list');
    }
    /**
     * @Route("/redirect/NonAuthorised", name="redirect_nonAuthorised")
     */
    public function nonAuthorised()
    {

        return $this->render('redirect/NonAuthorised.html.twig');
    }
}
