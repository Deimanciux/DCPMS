<?php

namespace App\Entity;

use App\Repository\ToothRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ToothRepository::class)]
class Tooth
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool $isRemoved;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool $isHealed;

    #[ORM\ManyToOne(targetEntity: Position::class, inversedBy: 'teeth')]
    #[ORM\JoinColumn(nullable: false)]
    private Position $position;

    #[ORM\OneToMany(mappedBy: 'tooth', targetEntity: HealthRecord::class)]
    private Collection $healthRecords;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'teeth')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    public function __construct()
    {
        $this->healthRecords = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsRemoved(): ?bool
    {
        return $this->isRemoved;
    }

    public function setIsRemoved(?bool $isRemoved): self
    {
        $this->isRemoved = $isRemoved;

        return $this;
    }

    public function getIsHealed(): ?bool
    {
        return $this->isHealed;
    }

    public function setIsHealed(?bool $isHealed): self
    {
        $this->isHealed = $isHealed;

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

    /**
     * @return Collection<int, HealthRecord>
     */
    public function getHealthRecords(): Collection
    {
        return $this->healthRecords;
    }

    public function addHealthRecord(HealthRecord $healthRecord): self
    {
        if (!$this->healthRecords->contains($healthRecord)) {
            $this->healthRecords[] = $healthRecord;
            $healthRecord->setTooth($this);
        }

        return $this;
    }

    public function removeHealthRecord(HealthRecord $healthRecord): self
    {
        if ($this->healthRecords->removeElement($healthRecord)) {
            // set the owning side to null (unless already changed)
            if ($healthRecord->getTooth() === $this) {
                $healthRecord->setTooth(null);
            }
        }

        return $this;
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
}
