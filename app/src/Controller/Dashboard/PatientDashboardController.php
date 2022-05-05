<?php

namespace App\Controller\Dashboard ;

use App\Controller\Dashboard\ClinicWorker\DoctorsCrudController;
use App\Controller\Dashboard\ClinicWorker\PatientCrudController;
use App\Controller\Dashboard\Doctor\PatientCrudController as DoctorPatientCrudController;
use App\Entity\Reservation;
use App\Entity\User;
use App\Form\ReservationType;
use App\Repository\ServiceRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
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
        if (isset($_GET['entityId'])) {
            $form = $this->createForm(ReservationType::class, new Reservation(), [
                'action' => $this->generateUrl('app_patient_reservation')
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
            ->addWebpackEncoreEntry('patientDashboard');
    }

//    public function configureActions(): Actions
//    {
//        return parent::configureActions()
//            ->setPermission(Action::NEW, User::ROLE_PATIENT)
//            ->setPermission(Action::EDIT, User::ROLE_PATIENT)
//            ->setPermission(Action::DELETE, User::ROLE_PATIENT)
//            ->setPermission(Action::DETAIL, User::ROLE_PATIENT);
//    }

    public function configureMenuItems(): iterable
    {
        if ($this->getUser() === null) {
            return;
        }

        if (in_array(User::ROLE_PATIENT, $this->getUser()->getRoles(), true)) {
            yield MenuItem::linkToRoute(
                'Reservations',
                'fa fa-calendar-check',
                'reservation_by_user',
                ['user' => $this->getUser()->getId()]
            )->setPermission(User::ROLE_PATIENT);

            yield MenuItem::linkToRoute(
                'Health records',
                'fa fa-file-medical',
                'app_health_records'
            )->setPermission(User::ROLE_PATIENT);
        }
        if (in_array(User::ROLE_CLINIC_WORKER, $this->getUser()->getRoles(), true)) {
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

        if (in_array(User::ROLE_DOCTOR, $this->getUser()->getRoles(), true)) {
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
        }
    }
}
