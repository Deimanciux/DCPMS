<?php

namespace App\Controller\PatientDashboard;

use App\Entity\HealthRecord;
use App\Entity\User;
use App\Repository\ServiceRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PatientDashboardController extends AbstractDashboardController
{
    public function __construct(
        private ServiceRepository $repository
    ) {
    }

    #[Route('/', name: 'patient_dashboard')]
    public function index(): Response
    {
        return $this->render('patient-dashboard/service.html.twig', [
            'services' => $this->repository->findActiveServices(),
        ]);
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
        yield MenuItem::linkToCrud(
            'Health Records',
            'fa fa-prescription-bottle',
            HealthRecord::class
        )->setPermission(User::ROLE_PATIENT);
        yield MenuItem::linkToRoute(
            'Reservations',
            'fa fa-calendar-check',
            'app_patient_reservation'
        )->setPermission(User::ROLE_PATIENT);
    }
}
