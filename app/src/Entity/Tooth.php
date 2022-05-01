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
    private bool $is_removed;

    #[ORM\OneToMany(mappedBy: 'tooth', targetEntity: HealthRecord::class)]
    private Collection $healthRecords;

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
        return $this->is_removed;
    }

    public function setIsRemoved(?bool $is_removed): self
    {
        $this->is_removed = $is_removed;

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
}
