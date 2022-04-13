<?php

declare(strict_types = 1);

namespace App\Controller\PatientDashboard\CustomPageController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/patient-reservations', name: 'app_patient_reservation')]
    public function index(): Response
    {
        return $this->render('patient-dashboard/reservation_calendar.html.twig');
    }
}
