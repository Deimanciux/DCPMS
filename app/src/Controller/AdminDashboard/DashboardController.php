<?php

namespace App\Controller\AdminDashboard;

use App\Entity\Diagnosis;
use App\Entity\HealthRecord;
use App\Entity\Position;
use App\Entity\Service;
use App\Entity\Tooth;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Dental Clinic AdminDashboard');
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()->addWebpackEncoreEntry('adminDashboard');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        if (!$user instanceof User) {
            throw new \Exception('Wrong user');
        }

        return parent::configureUserMenu($user)
            ->setAvatarUrl($user->getAvatarUrl());
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-dashboard');
        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud(
            'Health Records',
            'fa fa-prescription-bottle',
            HealthRecord::class
        );
        yield MenuItem::linkToCrud('Diagnosis', 'fa fa-book-medical', Diagnosis::class);
        yield MenuItem::linkToCrud('Positions', 'fas fa-compress-arrows-alt', Position::class);
        yield MenuItem::linkToCrud('Services', 'fas fa-stethoscope', Service::class)
            ->setController(ServiceCrudController::class);
        yield MenuItem::linkToCrud('Teeth', 'fas fa-tooth', Tooth::class);
        yield MenuItem::linkToCrud('Work Schedule', 'fas fa-calendar-alt', Tooth::class);
        yield MenuItem::linkToCrud('Registrations', 'fas fa-clipboard-list', Tooth::class);
        yield MenuItem::linkToRoute(
            'Health records creation',
            'fa fa-file-medical',
            'app_health_records_admin'
        )->setPermission(User::ROLE_PATIENT);
        yield MenuItem::linkToCrud('Patients', 'fas fa-clipboard-list', User::class)
              ->setController(PatientCrudController::class);
    }
}
