<?php

namespace App\Controller\Api;

use App\Repository\CourseRepository;
use App\Service\ProgressService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/progress')]
class ProgressController extends AbstractController
{
    public function __construct(private CourseRepository $courseRepository, private ProgressService $progressService)
    {
    }

    #[Route('/course/{id}', name: 'api_progress_course')]
    public function courseProgress(string $id): JsonResponse
    {
        $course = $this->courseRepository->find($id);
        if (!$course) {
            return $this->json(['error' => 'Course not found'], 404);
        }

        $value = $this->progressService->getCourseProgress($this->getUser(), $course);
        return $this->json(['courseId' => $id, 'progress' => $value]);
    }
}
