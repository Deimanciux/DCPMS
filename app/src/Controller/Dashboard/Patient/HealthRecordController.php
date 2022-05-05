<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Patient;

use App\Entity\User;
use App\Repository\HealthRecordRepository;
use App\Repository\PositionRepository;
use App\Voter\HealthRecordVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

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
        if ($this->isGranted(HealthRecordVoter::VIEW)) {
            return $this->json(['error' => true], Response::HTTP_FORBIDDEN);
        }

        /**@var User */
        $user = $this->getUser();

        $healthRecords = $this->healthRecordRepository->findBy(['user' => $user]);
        $positions = $this->positionRepository->findBySequenceNumber();

        return $this->render('dashboard/patient/health-record.html.twig', [
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

        $user = $this->getUser();

        $position = $this->positionRepository->findOneBy([
            'position' => $positionNumber
        ]);

        $healthRecords = $this->healthRecordRepository->findBy([
            'position' => $position,
            'user' => $patient
        ],
            ['updatedAt' => 'desc']
        );

        return $this->json ([
            'data' => $this->renderTemplateByRole($user, $healthRecords),
            'roles' => $user->getRoles()
            ]
        );
    }

    #[Route('/health-records/patient/{patient}', name: 'health_records_by_patient', methods: 'GET')]
    public function getTableTemplateByPatient(User $patient): Response
    {
        if ($this->isGranted(HealthRecordVoter::VIEW)) {
            return $this->json(['error' => true], Response::HTTP_FORBIDDEN);
        }

        $user = $this->getUser();

        if ($this->getUser() === null) {
            $this->redirect('login');
        }

        $healthRecords = $this->healthRecordRepository->findBy(['user' => $patient], ['updatedAt' => 'desc']);

        return $this->json (
            ['data' => $this->renderTemplateByRole($user, $healthRecords)]
        );
    }

    private function renderTemplateByRole(UserInterface $user, array $healthRecords): string
    {
        if (
            in_array(User::ROLE_DOCTOR, $user->getRoles(), true)
            || in_array(User::ROLE_ADMIN, $user->getRoles(), true)
        ) {
            return $this->renderView('dashboard/doctor/_health-record-table.html.twig', [
                'records' => $healthRecords,
            ]);
        }

        return $this->renderView('dashboard/patient/_health-record-table.html.twig', [
            'records' => $healthRecords
        ]);
    }
}
