<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    /**
     * @Route("/", name="project_list")
     */
    public function list(ProjectRepository $projectRepository, EntityManagerInterface $entityManager)
    {
        $projects = $projectRepository->getAllProjects();

        return $this->render('Project/list.html.twig', [
            'projects' => $projects
        ]);
    } 

    /**
     * @Route("/project/new", name="project_new")
     */
    public function new(Request $request, EntityManagerInterface $entityManager)
    {
        $project = new Project();
        $project->setStatus('Nouveau');
        $project->setStartedAt(new \DateTime());
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($project);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Nouveau projet '. $project->getName() .' créé !'
            );

            return $this->redirectToRoute('project_list');
        }

        return $this->render('Project/new.html.twig', [
            'projectForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/project/detail/{id}", name="project_detail")
     */
    public function detail($id, TaskRepository $taskRepository, ProjectRepository $projectRepository, Request $request, EntityManagerInterface $entityManager)
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
                'Status de projet '. $project->getName() .' a été mis à jour !'
            );

            return $this->render('Project/list.html.twig', [
                'projects' => $projects
            ]);
        }

        $tasks = $taskRepository->getTasksByProjectId($id);
        $project = $projectRepository->getProjectById($id);
        $status = ['Nouveau', 'En cours', 'Terminé'];

        return $this->render('Project/detail.html.twig', [
            'tasks' => $tasks,
            'project' => $project,
            'status' => $status
        ]);
    }

    /**
     * @Route("/project/update/{id}", name="project_update")
     */
    public function update($id, Request $request)
    {
        
    }



}