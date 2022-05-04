<?php

namespace App\Controller\PatientDashboard;

use App\Entity\HealthRecord;
use App\Entity\Reservation;
use App\Entity\Service;
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

            return $this->render('patient-dashboard/single-service.html.twig', [
                'form' => $form->createView(),
                'service' => $this->repository->findOneBy(['id' => $_GET['entityId']]),
            ]);
        }

        return $this->render('patient-dashboard/service.html.twig', [
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

    public function configureActions(): Actions
    {
        //CAN NOT DO CHANGES BUT SEE PAGE
        return parent::configureActions()
            ->setPermission(Action::NEW, User::ROLE_PATIENT)
            ->setPermission(Action::EDIT, User::ROLE_PATIENT)
            ->setPermission(Action::DELETE, User::ROLE_PATIENT)
            ->setPermission(Action::DETAIL, User::ROLE_PATIENT);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute(
            'Reservations',
            'fa fa-calendar-check',
            'app_patient_reservation'
        )->setPermission(User::ROLE_PATIENT);
        yield MenuItem::linkToRoute(
            'Health records',
            'fa fa-file-medical',
            'app_health_records'
        )->setPermission(User::ROLE_PATIENT);
    }
}
