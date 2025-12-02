<?php

namespace App\Entity;

use App\Enum\ProgressStatus;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: 'App\\Repository\\ProgressRepository')]
class Progress
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(['progress:read'])]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'progressRecords')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['progress:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Lesson::class, inversedBy: 'progressRecords')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['progress:read'])]
    private ?Lesson $lesson = null;

    #[ORM\Column(length: 30)]
    #[Groups(['progress:read'])]
    private ProgressStatus $status = ProgressStatus::NOT_STARTED;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['progress:read'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
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

    public function getStatus(): ProgressStatus
    {
        return $this->status;
    }

    public function setStatus(ProgressStatus $status): self
    {
        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
