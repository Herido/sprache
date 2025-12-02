<?php

namespace App\Controller;

use App\Repository\LessonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class LessonController extends AbstractController
{
    public function __construct(private readonly LessonRepository $lessonRepository)
    {
    }

    #[Route('/lessons/{id}', name: 'app_lesson_show')]
    public function show(string $id): Response
    {
        $lesson = $this->lessonRepository->find($id);
        if (!$lesson) {
            throw $this->createNotFoundException('Lektion nicht gefunden');
        }

        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
        ]);
    }
}
