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

        if ($projects !== null) {
            return $this->json($projects, 200, [], [
                AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'name',
                    'status',
                    'startedAt',
                    'endedAt'
                ]
            ], 200);
        }

        return $this->json([
            "status_code" => 404,
            "message" => "Not found",
        ], 404);
    }

    /**
     * @Route("/api/{id}/tasks", name="api_task")
     */
    public function task($id, TaskRepository $taskRepository)
    {
        $tasks = $taskRepository->getTasksById($id);

        if ($tasks != null) {
            return $this->json($tasks, 200, [], [
                AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'title',
                    'description'
                ]
            ], 200);
        }
        return $this->json([
            "status_code" => 404,
            "message" => "Not found. Please make sure the id is correct",
        ], 404);
    }
}