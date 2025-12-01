<?php

namespace App\Entity;

use App\Enum\UserRole;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\\Repository\\UserRepository')]
#[UniqueEntity('email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email = '';

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    #[Assert\NotBlank]
    private string $password = '';

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank]
    private string $name = '';

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Enrollment::class, cascade: ['persist', 'remove'])]
    private Collection $enrollments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: VocabularyEntry::class, cascade: ['persist', 'remove'])]
    private Collection $vocabularyEntries;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: QuizAttempt::class, cascade: ['persist', 'remove'])]
    private Collection $quizAttempts;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: LessonProgress::class, cascade: ['persist', 'remove'])]
    private Collection $lessonProgressRecords;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->enrollments = new ArrayCollection();
        $this->vocabularyEntries = new ArrayCollection();
        $this->quizAttempts = new ArrayCollection();
        $this->lessonProgressRecords = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = strtolower($email);
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (!in_array(UserRole::STUDENT->value, $roles, true)) {
            $roles[] = UserRole::STUDENT->value;
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function addRole(UserRole $role): self
    {
        if (!in_array($role->value, $this->roles, true)) {
            $this->roles[] = $role->value;
        }

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // no-op
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<int, Enrollment>
     */
    public function getEnrollments(): Collection
    {
        return $this->enrollments;
    }

    public function addEnrollment(Enrollment $enrollment): self
    {
        if (!$this->enrollments->contains($enrollment)) {
            $this->enrollments[] = $enrollment;
            $enrollment->setUser($this);
        }

        return $this;
    }

    public function removeEnrollment(Enrollment $enrollment): self
    {
        if ($this->enrollments->removeElement($enrollment) && $enrollment->getUser() === $this) {
            $enrollment->setUser(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, VocabularyEntry>
     */
    public function getVocabularyEntries(): Collection
    {
        return $this->vocabularyEntries;
    }

    /**
     * @return Collection<int, QuizAttempt>
     */
    public function getQuizAttempts(): Collection
    {
        return $this->quizAttempts;
    }

    /**
     * @return Collection<int, LessonProgress>
     */
    public function getLessonProgressRecords(): Collection
    {
        return $this->lessonProgressRecords;
    }
}
