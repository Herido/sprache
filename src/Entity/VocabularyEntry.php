<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\\Repository\\VocabularyEntryRepository')]
class VocabularyEntry
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'vocabularyEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank]
    private string $word = '';

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $translation = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $exampleSentence = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
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

    public function getWord(): string
    {
        return $this->word;
    }

    public function setWord(string $word): self
    {
        $this->word = $word;
        return $this;
    }

    public function getTranslation(): string
    {
        return $this->translation;
    }

    public function setTranslation(string $translation): self
    {
        $this->translation = $translation;
        return $this;
    }

    public function getExampleSentence(): ?string
    {
        return $this->exampleSentence;
    }

    public function setExampleSentence(?string $exampleSentence): self
    {
        $this->exampleSentence = $exampleSentence;
        return $this;
    }
}
