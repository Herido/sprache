<?php

namespace App\Service;

use App\Entity\Quiz;
use App\Entity\QuizQuestion;
use App\Repository\QuizRepository;

class QuizService
{
    public function __construct(private readonly QuizRepository $quizRepository)
    {
    }

    public function getQuiz(string $id): ?Quiz
    {
        return $this->quizRepository->find($id);
    }

    /**
     * @param Quiz $quiz
     * @param array<string,string> $submittedAnswers questionId => selected answer
     */
    public function evaluateQuiz(Quiz $quiz, array $submittedAnswers): array
    {
        $correct = 0;
        $total = $quiz->getQuestions()->count();
        /** @var QuizQuestion $question */
        foreach ($quiz->getQuestions() as $question) {
            $answer = $submittedAnswers[(string) $question->getId()] ?? null;
            if ($answer !== null && $answer === $question->getCorrectAnswer()) {
                $correct++;
            }
        }

        $score = $total > 0 ? (int) round(($correct / $total) * 100) : 0;

        return [
            'correct' => $correct,
            'total' => $total,
            'score' => $score,
        ];
    }
}
