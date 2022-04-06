<?php

namespace App\Entity;

use App\Repository\ToothRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ToothRepository::class)]
class Tooth
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $is_removed;

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
}
