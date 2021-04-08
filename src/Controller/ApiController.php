<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/projects", name="api_projects")
     */
    public function projects(ProjectRepository $projectRepository)
    {
        $projects = $projectRepository->getAllProjects();

        return $this->json($projects, 200, [], [
            AbstractNormalizer::ATTRIBUTES => [
                'id',
                'name',
                'status',
                'startedAt',
                'endedAt'
            ]
        ]);
    }

    /**
     * @Route("/api/{id}/tasks", name="api_task")
     */
    public function task($id, TaskRepository $taskRepository)
    {
        $tasks = $taskRepository->getTasksById($id);

        return $this->json($tasks, 200, [], [
            AbstractNormalizer::ATTRIBUTES => [
                'id',
                'title',
                'description',
            ]
        ]);
    }

    /**
     * @Route("/api/{id}/test", name="api_test")
     */
    public function test($id, TaskRepository $taskRepository)
    {
        $tasks = $taskRepository->getTasksByProjectId($id);
        return $this->json([
            'data' => $tasks
        ]);
    }
}