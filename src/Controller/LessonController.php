<?php

namespace App\Controller;

use App\Repository\LessonRepository;
use App\Service\ProgressService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lessons')]
class LessonController extends AbstractController
{
    public function __construct(private LessonRepository $lessonRepository, private ProgressService $progressService)
    {
    }

    #[Route('/{id}', name: 'lesson_show')]
    public function show(string $id): Response
    {
        $lesson = $this->lessonRepository->find($id);
        if (!$lesson) {
            throw $this->createNotFoundException();
        }

        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
        ]);
    }

    #[Route('/{id}/complete', name: 'lesson_complete')]
    public function complete(string $id): RedirectResponse
    {
        $lesson = $this->lessonRepository->find($id);
        $user = $this->getUser();
        if (!$lesson || !$user) {
            throw $this->createNotFoundException();
        }

        $progress = $this->progressService->completeLesson($user, $lesson);
        $this->addFlash('success', sprintf('Lektion abgeschlossen (%.1f%% Fortschritt).', $progress));

        return $this->redirectToRoute('lesson_show', ['id' => $id]);
    }
}
