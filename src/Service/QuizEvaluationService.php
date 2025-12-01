<?php

namespace App\Service;

use App\Entity\Quiz;
use App\Entity\QuizAttempt;
use App\Entity\User;
use App\Repository\QuizAttemptRepository;

class QuizEvaluationService
{
    public function __construct(private QuizAttemptRepository $quizAttemptRepository)
    {
    }

    public function evaluate(Quiz $quiz, User $user, array $submittedAnswers): QuizAttempt
    {
        $correct = 0;
        $total = max(1, count($quiz->getQuestions()));
        $answerRecord = [];

        foreach ($quiz->getQuestions() as $question) {
            $given = $submittedAnswers[(string) $question->getId()] ?? null;
            $isCorrect = $given === $question->getAnswer();
            $answerRecord[] = [
                'question' => $question->getPrompt(),
                'given' => $given,
                'expected' => $question->getAnswer(),
                'correct' => $isCorrect,
            ];

            if ($isCorrect) {
                ++$correct;
            }
        }

        $score = ($correct / $total) * 100;
        $attempt = (new QuizAttempt())
            ->setQuiz($quiz)
            ->setUser($user)
            ->setScore($score)
            ->setAnswers($answerRecord);

        $this->quizAttemptRepository->getEntityManager()->persist($attempt);
        $this->quizAttemptRepository->getEntityManager()->flush();

        return $attempt;
    }
}
