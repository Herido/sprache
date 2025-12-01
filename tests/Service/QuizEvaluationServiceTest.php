<?php

namespace App\Tests\Service;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\User;
use App\Repository\QuizAttemptRepository;
use App\Service\QuizEvaluationService;
use PHPUnit\Framework\TestCase;

class QuizEvaluationServiceTest extends TestCase
{
    public function testEvaluateCalculatesScoreAndPersistsAttempt(): void
    {
        $quiz = new Quiz();
        $question = (new Question())
            ->setQuiz($quiz)
            ->setPrompt('Test?')
            ->setChoices(['a', 'b'])
            ->setAnswer('a');
        $quiz->getQuestions()->add($question);

        $repo = $this->createMock(QuizAttemptRepository::class);
        $repo->method('getEntityManager')->willReturn(new class {
            public function persist($entity): void {}
            public function flush(): void {}
        });

        $service = new QuizEvaluationService($repo);
        $attempt = $service->evaluate($quiz, new User(), [(string) $question->getId() => 'a']);

        $this->assertSame(100.0, $attempt->getScore());
        $this->assertNotEmpty($attempt->getAnswers());
    }
}
