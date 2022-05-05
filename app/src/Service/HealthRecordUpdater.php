<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\HealthRecordDTO;
use App\Repository\HealthRecordRepository;
use Doctrine\ORM\EntityManagerInterface;

class HealthRecordUpdater
{
    public function __construct(
        private HealthRecordRepository $healthRecordRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function update(HealthRecordDTO $healthRecord): void
    {
        $existingHealthRecord = $this->healthRecordRepository->findOneBy(['id' => $healthRecord->getId()]);

        if ($existingHealthRecord === null) {
            return;
        }

        $existingHealthRecord
            ->setUser($healthRecord->getUser())
            ->setPosition($healthRecord->getPosition())
            ->setNotes($healthRecord->getNotes())
            ->setDiagnosis($healthRecord->getDiagnosis())
            ->setDoctor($healthRecord->getDoctor())
        ;

        $this->entityManager->flush();
    }
}
