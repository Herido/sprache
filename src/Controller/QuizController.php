<?php

namespace App\Controller;

use App\Service\ProgressService;
use App\Service\QuizService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class QuizController extends AbstractController
{
    public function __construct(
        private readonly QuizService $quizService,
        private readonly ProgressService $progressService
    ) {
    }

    #[Route('/quizzes/{id}', name: 'app_quiz_take', methods: ['GET', 'POST'])]
    public function take(string $id, Request $request): Response
    {
        $quiz = $this->quizService->getQuiz($id);
        if (!$quiz) {
            throw $this->createNotFoundException('Quiz nicht gefunden');
        }

        $result = null;
        if ($request->isMethod('POST')) {
            $answers = $request->request->all('answers');
            $result = $this->quizService->evaluateQuiz($quiz, $answers);
            if ($this->getUser() && $quiz->getCourse()) {
                $this->progressService->recordProgress($this->getUser(), $quiz->getCourse(), $result['score']);
            }
        }

        return $this->render('quiz/take.html.twig', [
            'quiz' => $quiz,
            'result' => $result,
        ]);
    }
}
