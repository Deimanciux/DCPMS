<?php

namespace App\Entity;

use App\Repository\WorkScheduleRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkScheduleRepository::class)]
class WorkSchedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $work_from;

    #[ORM\Column(type: 'datetime')]
    private $work_to;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorkFrom(): ?DateTimeInterface
    {
        return $this->work_from;
    }

    public function setWorkFrom(DateTimeInterface $work_from): self
    {
        $this->work_from = $work_from;

        return $this;
    }

    public function getWorkTo(): ?DateTimeInterface
    {
        return $this->work_to;
    }

    public function setWorkTo(DateTimeInterface $work_to): self
    {
        $this->work_to = $work_to;

        return $this;
    }
}
