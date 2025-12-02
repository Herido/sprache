<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\\Repository\\QuizRepository')]
class Quiz
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'quizzes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $title = '';

    /** @var Collection<int, QuizQuestion> */
    #[ORM\OneToMany(mappedBy: 'quiz', targetEntity: QuizQuestion::class, cascade: ['persist', 'remove'])]
    private Collection $questions;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->questions = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return Collection<int, QuizQuestion>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(QuizQuestion $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions->add($question);
            $question->setQuiz($this);
        }

        return $this;
    }

    public function removeQuestion(QuizQuestion $question): self
    {
        if ($this->questions->removeElement($question) && $question->getQuiz() === $this) {
            $question->setQuiz(null);
        }

        return $this;
    }
}
