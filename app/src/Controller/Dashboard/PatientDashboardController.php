<?php

namespace App\Controller\Dashboard ;

use App\Controller\Dashboard\Admin\DiagnosisCrudController;
use App\Controller\Dashboard\Admin\HealthRecordCrudController;
use App\Controller\Dashboard\Admin\PositionCrudController;
use App\Controller\Dashboard\Admin\ReservationCrudController;
use App\Controller\Dashboard\Admin\ServiceCrudController;
use App\Controller\Dashboard\Admin\ToothCrudController;
use App\Controller\Dashboard\Admin\UserCrudController;
use App\Controller\Dashboard\Admin\WorkScheduleCrudController;
use App\Controller\Dashboard\Doctor\WorkScheduleCrudController as DoctorWorkScheduleCrudController;
use App\Controller\Dashboard\ClinicWorker\DoctorsCrudController;
use App\Controller\Dashboard\ClinicWorker\PatientCrudController;
use App\Controller\Dashboard\Doctor\PatientCrudController as DoctorPatientCrudController;
use App\Entity\Diagnosis;
use App\Entity\HealthRecord;
use App\Entity\Position;
use App\Entity\Reservation;
use App\Entity\Service;
use App\Entity\Tooth;
use App\Entity\User;
use App\Entity\WorkSchedule;
use App\Form\ReservationType;
use App\Repository\ServiceRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class PatientDashboardController extends AbstractDashboardController
{
    public function __construct(
        private ServiceRepository $repository,
    ) {
    }

    #[Route('/', name: 'patient_dashboard')]
    public function index(): Response
    {
        if ($this->getUser() === null) {
            $userId = 1;
        } else {
            $userId = $this->getUser()->getId();
        }

        if (isset($_GET['entityId'])) {
        $form = $this->createForm(ReservationType::class, new Reservation(), [
            'action' => $this->generateUrl('reservation_by_user', ['user' => $userId])
        ]);

            return $this->render('dashboard/single-service.html.twig', [
                'form' => $form->createView(),
                'service' => $this->repository->findOneBy(['id' => $_GET['entityId']]),
            ]);
        }

        return $this->render('dashboard/service.html.twig', [
            'services' => $this->repository->findActiveServices(),
        ]);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        if (!$user instanceof User) {
            throw new \Exception('Wrong user');
        }

        return parent::configureUserMenu($user)
            ->setAvatarUrl($user->getAvatarUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Welcome To Dental Clinic');
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addWebpackEncoreEntry('patientDashboard')
        ->addWebpackEncoreEntry('appStyle');
    }

    public function configureMenuItems(): iterable
    {
        if ($this->getUser() !== null) {

            if (in_array(User::ROLE_PATIENT, $this->getUser()->getRoles(), true)) {
                foreach ( $this->getPatientMenuItems() as $menuItem) {
                    yield $menuItem;
                }
            }
            if (in_array(User::ROLE_CLINIC_WORKER, $this->getUser()->getRoles(), true)) {
                foreach ($this->getClinicWorkerMenuItems() as $menuItem) {
                    yield $menuItem;
                }
            }

            if (in_array(User::ROLE_DOCTOR, $this->getUser()->getRoles(), true)) {
                foreach ($this->getDoctorMenuItems() as $menuItem) {
                    yield $menuItem;
                }
            }

            if (in_array(User::ROLE_ADMIN, $this->getUser()->getRoles(), true)) {
                yield MenuItem::section('CLINIC WORKER');
                foreach ( $this->getClinicWorkerMenuItems() as $menuItem) {
                    yield $menuItem;
                }

                yield MenuItem::section('ADMIN');
                foreach ($this->getAdminMenuItems() as $menuItem) {
                    yield $menuItem;
                }
            }
        }

        return [];
    }

    private function getPatientMenuItems(): iterable
    {
        yield MenuItem::linkToRoute(
            'Reservations',
            'fa fa-calendar-check',
            'reservation_by_user',
            ['user' => $this->getUser()->getId()]
        );

        yield MenuItem::linkToRoute(
            'Health records',
            'fa fa-file-medical',
            'app_health_records'
        );
    }

    private function getClinicWorkerMenuItems(): iterable
    {
        yield MenuItem::linkToCrud(
            'Patients',
            'fas fa-clipboard-list',
            User::class
        )->setController(PatientCrudController::class);

        yield MenuItem::linkToCrud(
            'Doctors',
            'fa fa-stethoscope',
            User::class
        )->setController(DoctorsCrudController::class);
    }

    #[NoReturn]
    private function getDoctorMenuItems(): iterable
    {
        yield MenuItem::linkToRoute(
            'Reservations',
            'fa fa-calendar-check',
            'reservation_by_user',
            ['user' => $this->getUser()->getId()]
        );

        yield MenuItem::linkToCrud(
            'Patients',
            'fas fa-clipboard-list',
            User::class
        )->setController(DoctorPatientCrudController::class);

        yield MenuItem::linkToCrud(
            'Work Schedule',
            'fas fa-calendar-alt',
            WorkSchedule::class
        )->setController(DoctorWorkScheduleCrudController::class);
    }

    private function getAdminMenuItems(): iterable
    {
        yield MenuItem::linkToCrud(
            'Users',
            'fa fa-users',
            User::class
        )->setController(UserCrudController::class);

        yield MenuItem::linkToCrud(
            'Diagnosis',
            'fa fa-book-medical',
            Diagnosis::class
        )->setController(DiagnosisCrudController::class);

        yield MenuItem::linkToCrud(
            'Positions',
            'fas fa-compress-arrows-alt',
            Position::class
        )->setController(PositionCrudController::class);

        yield MenuItem::linkToCrud(
            'Services',
            'fa fa-suitcase',
            Service::class
        )->setController(ServiceCrudController::class);

        yield MenuItem::linkToCrud(
            'Registrations',
            'fas fa-clipboard-list',
            Tooth::class
        )->setController(ReservationCrudController::class);
    }
}
