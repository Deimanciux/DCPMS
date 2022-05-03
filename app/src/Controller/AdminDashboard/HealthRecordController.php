<?php

declare(strict_types=1);

namespace App\Controller\AdminDashboard;

use App\Entity\HealthRecord;
use App\Entity\User;
use App\Form\HealthRecordType;
use App\Repository\HealthRecordRepository;
use App\Repository\PositionRepository;
use App\Repository\UserRepository;
use App\Voter\HealthRecordVoter;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HealthRecordController extends AbstractController
{
    public function __construct(
        private HealthRecordRepository $healthRecordRepository,
        private PositionRepository $positionRepository,
        private EntityManagerInterface $entityManager,
        private AdminUrlGenerator $adminUrlGenerator,
        private UserRepository $userRepository
    ) {
    }

    #[Route('/health-records', name: 'app_health_records_admin')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $healthRecords = $this->healthRecordRepository->findAll();
        $positions = $this->positionRepository->findBySequenceNumber();

        return $this->render('admin/health-record.html.twig', [
            'records' => $healthRecords,
            'positions' => $positions
        ]);
    }

    #[Route('/health-records/{positionNumber}', name: 'app_health_records_by_position_admin')]
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

        $template = $this->renderView('admin/_health-record-table.html.twig', [
        'records' => $healthRecords,
        ]);

        return $this->json (
            ['data' => $template]
        );
    }

    //TO Do pataisyt is granted
    #[Route('/health-records/position/{positionNumber}/user/{user}', name: 'app_health_records_by_position_admin')]
    public function renderTableTemplateForPatientByUser(Request $request, int $positionNumber, User $user): Response
    {
        if ($this->isGranted(HealthRecordVoter::EDIT)) {
            return $this->json(['error' => true], Response::HTTP_FORBIDDEN);
        }

        $position = $this->positionRepository->findOneBy([
            'position' => $positionNumber
        ]);

        $healthRecords = $this->healthRecordRepository->findBy([
            'position' => $position,
            'user' => $user
        ]);

        $template = $this->renderView('admin/_health-record-table.html.twig', [
            'records' => $healthRecords,
        ]);

        return $this->json (
            ['data' => $template]
        );
    }

    //TO Do pataisyt is granted
    #[Route('/health-records/user/{user}', name: 'app_health_records_by_patient')]
    public function healthRecordByPatient(Request $request, User $user): Response
    {
        $form = $this->createForm(HealthRecordType::class);
        $healthRecords = $this->healthRecordRepository->findBy(['user' => $user]);
        $positions = $this->positionRepository->findBySequenceNumber();
        $form->handleRequest($request);
        $doctor = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var \App\Entity\HealthRecord
             */
            $healthRecord = $form->getData();
            $patientId = $form->get('user')->getData();
            $patient = $this->userRepository->findOneBy(['id' => $patientId]);
            $healthRecord->setUser($patient);
            $healthRecord->setDoctor($doctor);

            $this->entityManager->persist($healthRecord);
            $this->entityManager->flush();

            return $this->redirect(
                $this->adminUrlGenerator
                    ->setDashboard(DashboardController::class)
                    ->setController(self::class)
                    ->setRoute('app_health_records_by_patient', ['user' => $healthRecord->getUser()->getId()])
                    ->generateUrl()
            );
        }

        return $this->render('admin/health-record.html.twig', [
            'records' => $healthRecords,
            'positions' => $positions,
            'patient' => $user->getId(),
            'form' => $form->createView()
        ]);
    }

    //TO Do pataisyt is granted
    #[Route('/health-records/update/{user}', name: 'app_health_records_by_patient_update')]
    public function healthRecordByPatientUpdate(Request $request, User $user): Response
    {
        if ($this->isGranted(HealthRecordVoter::EDIT)) {
            return $this->json(['error' => true], Response::HTTP_FORBIDDEN);
        }

        $healthRecords = $this->healthRecordRepository->findBy(['user' => $user]);
        $template = $this->renderView('admin/_health-record-table.html.twig', ['records' => $healthRecords]);

        return $this->json (
            ['data' => $template]
        );
    }

    #[Route('/health-record/delete/{healthRecord}', name: 'app_health_records_by_patient_delete', methods:"DELETE")]
    public function deleteHealthRecord(HealthRecord $healthRecord): Response
    {
        if ($this->isGranted(HealthRecordVoter::EDIT)) {
            return $this->json(['error' => true], Response::HTTP_FORBIDDEN);
        }

        $this->entityManager->remove($healthRecord);
        $this->entityManager->flush();

        return $this->json(['success' => true], Response::HTTP_ACCEPTED);
    }
}
