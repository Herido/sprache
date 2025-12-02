<?php

namespace App\Entity;

use App\Enum\ExerciseType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\\Repository\\ExerciseRepository')]
class Exercise
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['lesson:read'])]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Lesson::class, inversedBy: 'exercises')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lesson $lesson = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['lesson:read'])]
    private string $question = '';

    #[ORM\Column(length: 50)]
    #[Groups(['lesson:read'])]
    private ExerciseType $type = ExerciseType::MULTIPLE_CHOICE;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['lesson:read'])]
    private string $answer = '';

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setLesson(?Lesson $lesson): self
    {
        $this->lesson = $lesson;
        return $this;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;
        return $this;
    }

    public function getType(): ExerciseType
    {
        return $this->type;
    }

    public function setType(ExerciseType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;
        return $this;
    }
}
