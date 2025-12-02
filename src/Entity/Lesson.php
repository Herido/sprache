<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\\Repository\\LessonRepository')]
class Lesson
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['lesson:read', 'course:read', 'progress:read'])]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['lesson:read'])]
    private ?Course $course = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    #[Groups(['lesson:read', 'course:read', 'progress:read'])]
    private string $title = '';

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['lesson:read'])]
    private string $content = '';

    /** @var Collection<int, Exercise> */
    #[ORM\OneToMany(mappedBy: 'lesson', targetEntity: Exercise::class, cascade: ['persist', 'remove'])]
    private Collection $exercises;

    /** @var Collection<int, Progress> */
    #[ORM\OneToMany(mappedBy: 'lesson', targetEntity: Progress::class, cascade: ['persist', 'remove'])]
    private Collection $progressRecords;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->exercises = new ArrayCollection();
        $this->progressRecords = new ArrayCollection();
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

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return Collection<int, Exercise>
     */
    public function getExercises(): Collection
    {
        return $this->exercises;
    }

    public function addExercise(Exercise $exercise): self
    {
        if (!$this->exercises->contains($exercise)) {
            $this->exercises[] = $exercise;
            $exercise->setLesson($this);
        }

        return $this;
    }

    public function removeExercise(Exercise $exercise): self
    {
        if ($this->exercises->removeElement($exercise) && $exercise->getLesson() === $this) {
            $exercise->setLesson(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Progress>
     */
    public function getProgressRecords(): Collection
    {
        return $this->progressRecords;
    }
}
