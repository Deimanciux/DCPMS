<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ReservationDTO;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;

class ReservationUpdater
{
    public function __construct(
        private ReservationRepository $reservationRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function update(ReservationDTO $reservation): void
    {
        $existingReservation = $this->reservationRepository->findOneBy(['id' => $reservation->getId()]);

        if ($existingReservation === null) {
            return;
        }

        $existingReservation
            ->setReasonOfVisit($reservation->getReasonOfVisit())
            ->setUser($reservation->getUser())
            ->setService($reservation->getService())
            ->setStartDate($reservation->getStartDate())
            ->setEndDate($reservation->getEndDate())
            ->setDoctor($reservation->getDoctor())
        ;

        $this->entityManager->flush();
    }
}
