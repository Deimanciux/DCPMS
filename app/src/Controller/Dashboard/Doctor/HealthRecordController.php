<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Doctor;

use App\Controller\Dashboard\PatientDashboardController;
use App\DTO\HealthRecordDTO;
use App\Entity\HealthRecord;
use App\Entity\Tooth;
use App\Entity\User;
use App\Form\HealthRecordType;
use App\Form\ToothTableType;
use App\Repository\HealthRecordRepository;
use App\Repository\PositionRepository;
use App\Repository\ToothRepository;
use App\Repository\UserRepository;
use App\Service\HealthRecordUpdater;
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
        private UserRepository $userRepository,
        private HealthRecordUpdater $recordUpdater,
        private ToothRepository $toothRepository
    ) {
    }

    #[Route('/health-records', name: 'app_health_records_admin')]
    public function index(): Response
    {
        if ($this->isGranted(HealthRecordVoter::VIEW)) {
            return $this->json(['error' => true], Response::HTTP_FORBIDDEN);
        }

        $healthRecords = $this->healthRecordRepository->findAll();
        $positions = $this->positionRepository->findBySequenceNumber();

        return $this->render('dashboard/doctor/health-record.html.twig', [
            'records' => $healthRecords,
            'positions' => $positions
        ]);
    }

    #[Route('/health-records/position/{positionNumber}/user/{user}', name: 'app_health_records_by_position_admin')]
    public function renderTableTemplateForPatientByUser(int $positionNumber, User $user): Response
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

    #[Route('/health-records/user/{user}', name: 'app_health_records_by_user')]
    public function healthRecordByUser(Request $request, User $user): Response
    {
        if ($this->isGranted(HealthRecordVoter::EDIT)) {
            return $this->json(['error' => true], Response::HTTP_FORBIDDEN);
        }

        $healthRecord = new HealthRecord();
        $form = $this->createForm(HealthRecordType::class, $healthRecord);
        $teethForm = $this->createForm(ToothTableType::class);
        $healthRecords = $user->getHealthRecords();
        $teethBySequence = $this->toothRepository->getTeethBySequence($user);
        $form->handleRequest($request);
        $teethForm->handleRequest($request);
        $doctor = $this->getUser();

        if ($teethForm->isSubmitted() && $teethForm->isValid()) {
            $teeth = $teethForm->getData();
            $positions = $this->positionRepository->findAll();

            foreach ($positions as $position) {
                $tooth = new Tooth();
                $tooth->setIsRemoved(!$teeth['tooth'.$position->getPosition()]);
                $tooth->setPosition($position);
                $tooth->setUser($user);
                $this->entityManager->persist($tooth);
            }

            $this->entityManager->flush();

            return $this->redirect(
                $this->adminUrlGenerator
                    ->setDashboard(PatientDashboardController::class)
                    ->setController(self::class)
                    ->setRoute('app_health_records_by_user', ['user' => $user->getId()])
                    ->generateUrl()
            );
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $patientId = $form->get('user')->getData();
            $patient = $this->userRepository->findOneBy(['id' => $patientId]);
            $healthRecord->setUser($patient);
            $healthRecord->setDoctor($doctor);

            if ($healthRecord->getId() !== null) {

                $this->recordUpdater->update(
                    new HealthRecordDTO(
                        $healthRecord->getId(),
                        $healthRecord->getUser(),
                        $healthRecord->getTooth(),
                        $healthRecord->getNotes(),
                        $healthRecord->getDiagnosis(),
                        $healthRecord->getDoctor(),
                    )
                );

                return $this->redirect(
                    $this->adminUrlGenerator
                        ->setDashboard(PatientDashboardController::class)
                        ->setController(self::class)
                        ->setRoute('app_health_records_by_user', ['user' => $healthRecord->getUser()->getId()])
                        ->generateUrl()
                );
            }


            $this->entityManager->persist($healthRecord);
            $this->entityManager->flush();

            return $this->redirect(
                $this->adminUrlGenerator
                    ->setDashboard(PatientDashboardController::class)
                    ->setController(self::class)
                    ->setRoute('app_health_records_by_user', ['user' => $healthRecord->getUser()->getId()])
                    ->generateUrl()
            );
        }

        return $this->render('dashboard/doctor/health-record.html.twig', [
            'records' => $healthRecords,
            'teeth' => $teethBySequence,
            'patient' => $user->getId(),
            'patientUser' => $user,
            'form' => $form->createView(),
            'teethForm' => $teethForm->createView()
        ]);
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

    #[Route('/health-record/{healthRecord}', name: 'app_health_record', methods:"GET")]
    public function getHealthRecord(?HealthRecord $healthRecord): Response
    {
        if ($this->isGranted(HealthRecordVoter::EDIT)) {
            return $this->json(['error' => true], Response::HTTP_FORBIDDEN);
        }

        if ($healthRecord === null) {
            return $this->json(['error' => true], Response::HTTP_NOT_FOUND);
        }

        return $this->json (
            ['data' => [
                'id' => $healthRecord->getId(),
                'user' => $healthRecord->getUser()->getId(),
                'tooth' => $healthRecord->getTooth()->getId(),
                'notes' => $healthRecord->getNotes(),
                'diagnosis' => $healthRecord->getDiagnosis()->getId(),
                'doctor' => $healthRecord->getDoctor()->getId(),
                'isRemoved' => $healthRecord->getTooth()->getIsRemoved()
            ]]
        );
    }
}
