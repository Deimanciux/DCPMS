<?php

declare(strict_types = 1);

namespace App\Controller\PatientDashboard\CustomPageController;

use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/patient-reservations', name: 'app_patient_reservation')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ReservationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $reservation = $form->getData();
            $reservation->setUser($user);
            $this->entityManager->persist($reservation);
            $this->entityManager->flush();
        }

        return $this->render('patient-dashboard/reservation_calendar.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
