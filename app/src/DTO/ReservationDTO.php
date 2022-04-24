<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Service;
use Symfony\Component\Security\Core\User\UserInterface;

class ReservationDTO
{
    public function __construct(
        private int $id,
        private ?string $title,
        private UserInterface $user,
        private ?Service $service,
        private \DateTimeInterface $startDate,
        private \DateTimeInterface $endDate,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }
}
