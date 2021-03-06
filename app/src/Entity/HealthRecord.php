<?php

namespace App\Entity;

use App\Repository\HealthRecordRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: HealthRecordRepository::class)]
class HealthRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'healthRecords')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'doctorHealthRecords')]
    #[ORM\JoinColumn(nullable: false)]
    private UserInterface $doctor;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $notes;

    #[ORM\ManyToOne(targetEntity: Diagnosis::class, inversedBy: 'healthRecords')]
    private Diagnosis $diagnosis;

    #[ORM\ManyToOne(targetEntity: Position::class)]
    private Position $position;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Tooth::class, inversedBy: 'healthRecords')]
    private Tooth $tooth;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getDiagnosis(): ?Diagnosis
    {
        return $this->diagnosis;
    }

    public function setDiagnosis(?Diagnosis $diagnosis): self
    {
        $this->diagnosis = $diagnosis;

        return $this;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(?Position $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable('now'));
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable('now'));
        }
    }

    public function getDoctor(): ?UserInterface
    {
        return $this->doctor;
    }

    public function setDoctor(?UserInterface $user): self
    {
        $this->doctor = $user;

        return $this;
    }

    public function getTooth(): Tooth
    {
        return $this->tooth;
    }

    public function setTooth(Tooth $tooth): self
    {
        $this->tooth = $tooth;

        return $this;
    }
}
