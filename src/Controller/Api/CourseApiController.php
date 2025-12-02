<?php

namespace App\Controller\Api;

use App\Service\CourseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api')]
#[IsGranted('ROLE_USER')]
class CourseApiController extends AbstractController
{
    public function __construct(private readonly CourseService $courseService)
    {
    }

    #[Route('/courses', name: 'api_courses', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $payload = array_map(static function ($course) {
            return [
                'id' => (string) $course->getId(),
                'title' => $course->getTitle(),
                'description' => $course->getDescription(),
                'level' => $course->getLevel()->value,
            ];
        }, $this->courseService->listCourses());

        return $this->json($payload);
    }

    #[Route('/courses/{id}', name: 'api_course_detail', methods: ['GET'])]
    public function detail(string $id): JsonResponse
    {
        $course = $this->courseService->getCourse($id);
        if (!$course) {
            return $this->json(['message' => 'Kurs nicht gefunden'], JsonResponse::HTTP_NOT_FOUND);
        }

        $lessons = [];
        foreach ($course->getLessons() as $lesson) {
            $lessons[] = [
                'id' => (string) $lesson->getId(),
                'title' => $lesson->getTitle(),
            ];
        }

        return $this->json([
            'id' => (string) $course->getId(),
            'title' => $course->getTitle(),
            'description' => $course->getDescription(),
            'level' => $course->getLevel()->value,
            'lessons' => $lessons,
        ]);
    }
}
