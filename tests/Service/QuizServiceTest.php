<?php

namespace App\Tests\Service;

use App\Entity\Quiz;
use App\Entity\QuizQuestion;
use App\Service\QuizService;
use PHPUnit\Framework\TestCase;

class QuizServiceTest extends TestCase
{
    public function testEvaluationCalculatesScore(): void
    {
        $quiz = new Quiz();
        $question = (new QuizQuestion())
            ->setQuiz($quiz)
            ->setQuestion('Test?')
            ->setAnswers(['a', 'b'])
            ->setCorrectAnswer('a');

        $quiz->getQuestions()->add($question);

        $service = new QuizService($this->createMock('App\\Repository\\QuizRepository'));
        $result = $service->evaluateQuiz($quiz, [(string) $question->getId() => 'a']);

        $this->assertSame(1, $result['correct']);
        $this->assertSame(1, $result['total']);
        $this->assertSame(100, $result['score']);
    }
}
