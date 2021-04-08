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
                'Nouveau projet "'. $project->getName() .'" a été créé !'
            );

            return $this->redirectToRoute('project_list');
        }

        return $this->render('Project/new.html.twig', [
            'projectForm' => $form->createView()
        ]);
    }
}