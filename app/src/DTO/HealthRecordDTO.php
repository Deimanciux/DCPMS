<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Diagnosis;
use App\Entity\Tooth;
use Symfony\Component\Security\Core\User\UserInterface;

class HealthRecordDTO
{
    public function __construct(
        private int $id,
        private UserInterface $user,
        private Tooth $tooth,
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

    public function getTooth(): Tooth
    {
        return $this->tooth;
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
