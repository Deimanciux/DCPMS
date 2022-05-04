<?php

declare(strict_types=1);

namespace App\Controller\PatientDashboard;

use App\Entity\User;
use App\Repository\HealthRecordRepository;
use App\Repository\PositionRepository;
use App\Voter\HealthRecordVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HealthRecordController extends AbstractController
{
    public function __construct(
        private HealthRecordRepository $healthRecordRepository,
        private PositionRepository $positionRepository,
    ) {
    }

    #[Route('/health-records/patient', name: 'app_health_records')]
    public function index(): Response
    {
        /**@var User */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirect('login');
        }

        $healthRecords = $this->healthRecordRepository->findBy(['user' => $user]);
        $positions = $this->positionRepository->findBySequenceNumber();

        return $this->render('patient-dashboard/health-record.html.twig', [
            'records' => $healthRecords,
            'positions' => $positions,
            'patient' => $user->getId()
        ]);
    }

    #[Route('/health-records/patient/{patient}/position/{positionNumber}/', name: 'health_records_by_patient_user')]
    public function getTableTemplateByPositionAndUser(User $patient, int $positionNumber): Response
    {
        if ($this->isGranted(HealthRecordVoter::VIEW)) {
            return $this->json(['error' => true], Response::HTTP_FORBIDDEN);
        }

        $position = $this->positionRepository->findOneBy([
            'position' => $positionNumber
        ]);

        $healthRecords = $this->healthRecordRepository->findBy([
            'position' => $position,
            'user' => $patient
        ]);

        $template = $this->renderView('patient-dashboard/_health-record-table.html.twig', [
            'records' => $healthRecords,
        ]);

        return $this->json (
            ['data' => $template]
        );
    }

    #[Route('/health-records/patient/{patient}', name: 'health_records_by_patient', methods: 'GET')]
    public function getTableTemplateByPatient(User $patient): Response
    {
        if ($this->isGranted(HealthRecordVoter::VIEW)) {
            return $this->json(['error' => true], Response::HTTP_FORBIDDEN);
        }

        $healthRecords = $this->healthRecordRepository->findBy(['user' => $patient]);
        $template = $this->renderView('patient-dashboard/_health-record-table.html.twig', [
            'records' => $healthRecords
        ]);

        return $this->json (
            ['data' => $template]
        );
    }
}
