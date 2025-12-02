<?php

namespace App\Controller;

use App\Repository\ProgressRepository;
use App\Service\CourseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    public function __construct(private readonly CourseService $courseService, private readonly ProgressRepository $progressRepository)
    {
    }

    #[Route('/', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        $user = $this->getUser();
        $progress = $user ? $this->progressRepository->findBy(['user' => $user]) : [];

        return $this->render('dashboard/index.html.twig', [
            'courses' => $this->courseService->listCourses(),
            'progress' => $progress,
        ]);
    }
}
