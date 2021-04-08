<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/task/{id}", name="task_list")
     */
    public function list($id, TaskRepository $taskRepository, ProjectRepository $projectRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $project = $projectRepository->getProjectById($id);

        if ($request->getMethod() === 'POST' && $project->getStatus() != 'Terminé') {
            $status = $request->request->get('status');

            if ($status != $project->getStatus()) {
                $project->setStatus($status);
                $project->setEndedAt(new \DateTime());

                $entityManager->persist($project);
                $entityManager->flush();

            } if ($status == 'Terminé') {
                $project->setStatus('Terminé');
                $project->setEndedAt(new \DateTime());

                $entityManager->persist($project);
                $entityManager->flush();
            }
            
            $projects = $projectRepository->getAllProjects();

            $this->addFlash(
                'notice',
                'Changement de statut "'.$project->getName().'" : '.$project->getStatus()
            );

            return $this->render('Project/list.html.twig', [
                'projects' => $projects
            ]);
        }

        $tasks = $taskRepository->getTasksByProjectId($id);
        $project = $projectRepository->getProjectById($id);
        $status = ['Nouveau', 'En cours', 'Terminé'];

        return $this->render('Task/list.html.twig', [
            'tasks' => $tasks,
            'project' => $project,
            'status' => $status
        ]);
    }

    /**
     * @Route("/task/{id}/new", name="task_new")
     */
    public function new($id, Request $request, ProjectRepository $projectRepository, EntityManagerInterface $entityManager){
        $task = new Task();
        $project = $projectRepository->getProjectById($id);
        $task->setProject($project);
        $task->setStartedAt(new \DateTime());
        
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Nouvelle tâche "'. $task->getTitle() .'" a été créée !'
            );

            return $this->redirectToRoute('task_list',[
                'id' => $id
            ]);
        }

        return $this->render('Task/new.html.twig', [
            'taskForm' => $form->createView(),
            'project' => $project
        ]);
    }
}