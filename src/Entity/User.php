<?php

namespace App\Entity;

use App\Enum\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\\Repository\\UserRepository')]
#[UniqueEntity(fields: ['email'])]
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

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /** @var Collection<int, Progress> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Progress::class, cascade: ['persist', 'remove'])]
    private Collection $progressEntries;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->createdAt = new \DateTimeImmutable();
        $this->progressEntries = new ArrayCollection();
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
        if (!in_array(Role::ROLE_USER->value, $roles, true)) {
            $roles[] = Role::ROLE_USER->value;
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function addRole(Role $role): self
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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, Progress>
     */
    public function getProgressEntries(): Collection
    {
        return $this->progressEntries;
    }

    public function addProgress(Progress $progress): self
    {
        if (!$this->progressEntries->contains($progress)) {
            $this->progressEntries->add($progress);
            $progress->setUser($this);
        }

        return $this;
    }

    public function removeProgress(Progress $progress): self
    {
        if ($this->progressEntries->removeElement($progress) && $progress->getUser() === $this) {
            $progress->setUser(null);
        }

        return $this;
    }
}
