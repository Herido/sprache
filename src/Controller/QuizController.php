<?php

namespace App\Controller;

use App\Repository\QuizRepository;
use App\Service\QuizEvaluationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/quizzes')]
class QuizController extends AbstractController
{
    public function __construct(private QuizRepository $quizRepository, private QuizEvaluationService $quizEvaluationService)
    {
    }

    #[Route('/{id}', name: 'quiz_take')]
    public function take(string $id, Request $request): Response
    {
        $quiz = $this->quizRepository->find($id);
        if (!$quiz) {
            throw $this->createNotFoundException();
        }

        if ($request->isMethod('POST')) {
            $attempt = $this->quizEvaluationService->evaluate($quiz, $this->getUser(), $request->request->all());

            return $this->render('quiz/result.html.twig', [
                'quiz' => $quiz,
                'attempt' => $attempt,
            ]);
        }

        return $this->render('quiz/take.html.twig', [
            'quiz' => $quiz,
        ]);
    }
}
