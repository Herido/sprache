<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use App\Repository\ProgressRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(
        CourseRepository $courseRepository,
        LessonRepository $lessonRepository,
        ProgressRepository $progressRepository
    ): Response {
        return $this->render('dashboard/index.html.twig', [
            'courses' => $courseRepository->findAll(),
            'lessons' => $lessonRepository->findBy([], null, 5),
            'progress' => $progressRepository->findAll(),
        ]);
    }
}
