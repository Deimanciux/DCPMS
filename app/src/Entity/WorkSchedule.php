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
    private int $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $workFrom;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $workTo;

    #[ORM\Column(type: 'integer')]
    private int $weekDay;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'workSchedules')]
    private User $user;

    public function getId(): int
    {
        return $this->id;
    }

    public function getWorkFrom(?\DateTimeImmutable $date = null): DateTimeInterface
    {
        if ($date !== null) {
            $currentDate = $date->format('Y-m-d');

            return new \DateTimeImmutable($currentDate .' '. $this->workFrom->format('H:i'));
        }

        $currentDate = (new \DateTimeImmutable())->format('Y-m-d');

        return new \DateTimeImmutable($currentDate .' '. $this->workFrom->format('H:i'));
    }

    public function setWorkFrom(DateTimeInterface $workFrom): self
    {
        $this->workFrom = $workFrom;

        return $this;
    }

    public function getWorkTo(?\DateTimeImmutable $date = null): DateTimeInterface
    {
        if ($date !== null) {
            $currentDate = $date->format('Y-m-d');

            return new \DateTimeImmutable($currentDate .' '. $this->workTo->format('H:i'));
        }

        $currentDate = (new \DateTimeImmutable())->format('Y-m-d');

        return new \DateTimeImmutable($currentDate .' '. $this->workTo->format('H:i'));
    }

    public function setWorkTo(DateTimeInterface $workTo): self
    {
        $this->workTo = $workTo;

        return $this;
    }

    public function getWeekDay(): ?int
    {
        return $this->weekDay;
    }

    public function setWeekDay(int $weekDay): self
    {
        $this->weekDay = $weekDay;

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
