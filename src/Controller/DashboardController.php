<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Repository\QuizAttemptRepository;
use App\Service\ProgressService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private CourseRepository $courseRepository,
        private QuizAttemptRepository $quizAttemptRepository,
        private ProgressService $progressService
    ) {
    }

    #[Route('/', name: 'dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();
        $courses = $this->courseRepository->findAll();
        $attempts = $this->quizAttemptRepository->findBy(['user' => $user], ['submittedAt' => 'DESC'], 5);

        $progress = [];
        foreach ($courses as $course) {
            $progress[(string) $course->getId()] = $this->progressService->getCourseProgress($user, $course);
        }

        return $this->render('dashboard/index.html.twig', [
            'courses' => $courses,
            'progress' => $progress,
            'attempts' => $attempts,
        ]);
    }
}
