<?php

namespace App\Controller\PatientDashboard;

use App\Entity\HealthRecord;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PatientDashboardController extends AbstractDashboardController
{
    #[Route('/', name: 'patient_dashboard')]
    public function index(): Response
    {
        return $this->render('patientDashboard/service.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Welcome To Dental Clinic');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Health Records', 'fa fa-prescription-bottle', HealthRecord::class);
    }
}
