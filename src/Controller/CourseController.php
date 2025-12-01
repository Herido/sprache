<?php

namespace App\Controller;

use App\Enum\CourseLevel;
use App\Repository\CourseRepository;
use App\Service\ProgressService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/courses')]
class CourseController extends AbstractController
{
    public function __construct(private CourseRepository $courseRepository, private ProgressService $progressService)
    {
    }

    #[Route('/', name: 'course_index')]
    public function index(): Response
    {
        $courses = $this->courseRepository->findAll();
        return $this->render('course/index.html.twig', [
            'courses' => $courses,
        ]);
    }

    #[Route('/level/{level}', name: 'course_by_level')]
    public function byLevel(string $level): Response
    {
        $courses = $this->courseRepository->findByLevel(CourseLevel::from($level));
        return $this->render('course/index.html.twig', [
            'courses' => $courses,
        ]);
    }

    #[Route('/{id}', name: 'course_show')]
    public function show(string $id): Response
    {
        $course = $this->courseRepository->find($id);
        if (!$course) {
            throw $this->createNotFoundException();
        }

        return $this->render('course/show.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/{id}/enroll', name: 'course_enroll')]
    public function enroll(string $id): RedirectResponse
    {
        $course = $this->courseRepository->find($id);
        $user = $this->getUser();
        if (!$course || !$user) {
            throw $this->createNotFoundException();
        }

        $this->progressService->enroll($user, $course);
        $this->addFlash('success', 'Du bist erfolgreich eingeschrieben!');

        return $this->redirectToRoute('course_show', ['id' => $id]);
    }
}
