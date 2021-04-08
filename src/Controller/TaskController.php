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
     * @Route("/task/new/{id}", name="task_new")
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
                'Nouvelle tâche '. $task->getTitle() .' créée !'
            );


            return $this->redirectToRoute('project_detail',[
                'id' => $id
            ]);
        }

        return $this->render('Task/new.html.twig', [
            'taskForm' => $form->createView()
        ]);
    }
}