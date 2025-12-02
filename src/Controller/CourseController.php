<?php

namespace App\Controller;

use App\Service\CourseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class CourseController extends AbstractController
{
    public function __construct(private readonly CourseService $courseService)
    {
    }

    #[Route('/courses', name: 'app_course_list')]
    public function list(): Response
    {
        return $this->render('course/list.html.twig', [
            'courses' => $this->courseService->listCourses(),
        ]);
    }

    #[Route('/courses/{id}', name: 'app_course_detail')]
    public function detail(string $id): Response
    {
        $course = $this->courseService->getCourse($id);
        if (!$course) {
            throw $this->createNotFoundException('Kurs nicht gefunden');
        }

        return $this->render('course/detail.html.twig', [
            'course' => $course,
        ]);
    }
}
