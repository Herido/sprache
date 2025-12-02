<?php

namespace App\Entity;

use App\Enum\CourseLevel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\\Repository\\CourseRepository')]
class Course
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $title = '';

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private string $description = '';

    #[ORM\Column(type: 'string', enumType: CourseLevel::class)]
    private CourseLevel $level = CourseLevel::BEGINNER;

    /** @var Collection<int, Lesson> */
    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Lesson::class, cascade: ['persist', 'remove'])]
    private Collection $lessons;

    /** @var Collection<int, Quiz> */
    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Quiz::class, cascade: ['persist', 'remove'])]
    private Collection $quizzes;

    /** @var Collection<int, Vocabulary> */
    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Vocabulary::class, cascade: ['persist', 'remove'])]
    private Collection $vocabularies;

    /** @var Collection<int, Progress> */
    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Progress::class, cascade: ['persist', 'remove'])]
    private Collection $progressEntries;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->lessons = new ArrayCollection();
        $this->quizzes = new ArrayCollection();
        $this->vocabularies = new ArrayCollection();
        $this->progressEntries = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getLevel(): CourseLevel
    {
        return $this->level;
    }

    public function setLevel(CourseLevel $level): self
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @return Collection<int, Lesson>
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    public function addLesson(Lesson $lesson): self
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons->add($lesson);
            $lesson->setCourse($this);
        }

        return $this;
    }

    public function removeLesson(Lesson $lesson): self
    {
        if ($this->lessons->removeElement($lesson) && $lesson->getCourse() === $this) {
            $lesson->setCourse(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Quiz>
     */
    public function getQuizzes(): Collection
    {
        return $this->quizzes;
    }

    public function addQuiz(Quiz $quiz): self
    {
        if (!$this->quizzes->contains($quiz)) {
            $this->quizzes->add($quiz);
            $quiz->setCourse($this);
        }

        return $this;
    }

    public function removeQuiz(Quiz $quiz): self
    {
        if ($this->quizzes->removeElement($quiz) && $quiz->getCourse() === $this) {
            $quiz->setCourse(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Vocabulary>
     */
    public function getVocabularies(): Collection
    {
        return $this->vocabularies;
    }

    public function addVocabulary(Vocabulary $vocabulary): self
    {
        if (!$this->vocabularies->contains($vocabulary)) {
            $this->vocabularies->add($vocabulary);
            $vocabulary->setCourse($this);
        }

        return $this;
    }

    public function removeVocabulary(Vocabulary $vocabulary): self
    {
        if ($this->vocabularies->removeElement($vocabulary) && $vocabulary->getCourse() === $this) {
            $vocabulary->setCourse(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Progress>
     */
    public function getProgressEntries(): Collection
    {
        return $this->progressEntries;
    }
}
