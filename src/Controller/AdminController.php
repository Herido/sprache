<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_index')]
    public function index(
        UserRepository $userRepository,
        CourseRepository $courseRepository,
        LessonRepository $lessonRepository
    ): Response {
        return $this->render('admin/index.html.twig', [
            'users' => $userRepository->findAll(),
            'courses' => $courseRepository->findAll(),
            'lessons' => $lessonRepository->findAll(),
        ]);
    }
}
