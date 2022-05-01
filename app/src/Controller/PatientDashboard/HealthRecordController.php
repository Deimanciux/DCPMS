<?php

declare(strict_types=1);

namespace App\Controller\PatientDashboard;

use App\Repository\HealthRecordRepository;
use App\Repository\PositionRepository;
use App\Voter\HealthRecordVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HealthRecordController extends AbstractController
{
    public function __construct(
        private HealthRecordRepository $healthRecordRepository,
        private PositionRepository $positionRepository
    ) {
    }

    #[Route('/health-records', name: 'app_health_records')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $healthRecords = $this->healthRecordRepository->findAll();
        $positions = $this->positionRepository->findBySequenceNumber();

        return $this->render('patient-dashboard/health-record.html.twig', [
            'records' => $healthRecords,
            'positions' => $positions
        ]);
    }

    #[Route('/health-records/{positionNumber}', name: 'app_health_records_by_position')]
    public function renderTableTemplateForPosition(Request $request, int $positionNumber): Response
    {
        if ($this->isGranted(HealthRecordVoter::EDIT)) {
            return $this->json(['error' => true], Response::HTTP_FORBIDDEN);
        }

        $position = $this->positionRepository->findOneBy([
            'position' => $positionNumber
        ]);

        $healthRecords = $this->healthRecordRepository->findBy([
            'position' => $position
        ]);

        $template = $this->renderView('patient-dashboard/_health-record-table.html.twig', [
        'records' => $healthRecords,
        ]);

        return $this->json (
            ['data' => $template]
        );
    }
}
