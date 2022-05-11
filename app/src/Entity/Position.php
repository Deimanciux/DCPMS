<?php

namespace App\Entity;

use App\Repository\PositionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PositionRepository::class)]
class Position
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $position;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'string', length: 50)]
    private string $title;

    #[ORM\Column(type: 'integer')]
    private int $sequenceNumber;

    #[ORM\OneToMany(mappedBy: 'position', targetEntity: Tooth::class, orphanRemoval: true)]
    private Collection $teeth;

    public function __construct()
    {
        $this->teeth = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSequenceNumber(): ?int
    {
        return $this->sequenceNumber;
    }

    public function setSequenceNumber(int $sequenceNumber): self
    {
        $this->sequenceNumber = $sequenceNumber;

        return $this;
    }

    /**
     * @return Collection<int, Tooth>
     */
    public function getTeeth(): Collection
    {
        return $this->teeth;
    }

    public function addTooth(Tooth $tooth): self
    {
        if (!$this->teeth->contains($tooth)) {
            $this->teeth[] = $tooth;
            $tooth->setPosition($this);
        }

        return $this;
    }

    public function removeTooth(Tooth $tooth): self
    {
        if ($this->teeth->removeElement($tooth)) {
            // set the owning side to null (unless already changed)
            if ($tooth->getPosition() === $this) {
                $tooth->setPosition(null);
            }
        }

        return $this;
    }
}
