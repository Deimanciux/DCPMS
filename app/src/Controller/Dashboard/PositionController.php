<?php

declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Repository\PositionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PositionController extends AbstractController
{
    public function __construct(
        private PositionRepository $positionRepository
    ) {
    }

    #[Route('/positions', name: 'get_positions', methods:"GET")]
    public function getReservations(): Response
    {
        $positions  = $this->positionRepository->findAll();

        return $this->json (
            ['data' => $this->generateResponse($positions)]
        );
    }

    private function generateResponse(array $positions): array
    {
        $data = [];

        foreach ($positions as $position) {
            $data[] = [
                'position' => $position->getPosition(),
                'id' => $position->getId(),
            ];
        }

        return $data;
    }
}
