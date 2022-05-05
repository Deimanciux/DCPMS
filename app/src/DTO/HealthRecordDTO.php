<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Diagnosis;
use App\Entity\Position;
use Symfony\Component\Security\Core\User\UserInterface;

class HealthRecordDTO
{
    public function __construct(
        private int $id,
        private UserInterface $user,
        private Position $position,
        private string $notes,
        private Diagnosis $diagnosis,
        private UserInterface $doctor
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function getNotes(): string
    {
        return $this->notes;
    }

    public function getDiagnosis(): Diagnosis
    {
        return $this->diagnosis;
    }

    public function getDoctor(): UserInterface
    {
        return $this->doctor;
    }
}
