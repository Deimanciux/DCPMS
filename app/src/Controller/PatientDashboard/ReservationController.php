<?php

declare(strict_types = 1);

namespace App\Controller\PatientDashboard;

use App\DTO\ReservationDTO;
use App\Entity\Reservation;
use App\Entity\Service;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\ServiceRepository;
use App\Service\ReservationUpdater;
use App\Voter\ReservationVoter;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ReservationUpdater $reservationUpdater,
        private ReservationRepository $reservationRepository,
        private ServiceRepository $serviceRepository,
        private AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    #[Route('/patient-reservations', name: 'app_patient_reservation')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ReservationType::class, new Reservation(), [
            'action' => $this->generateUrl('app_patient_reservation')
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            /**
             * @var Reservation
             */
            $reservation = $form->getData();
            $reservation->setUser($user);
            $reservation->setEndDate($this->getEndDate($reservation));

            if ($reservation->getId() !== null) {

                $this->reservationUpdater->update(
                    new ReservationDTO(
                        $reservation->getId(),
                        $reservation->getTitle(),
                        $reservation->getUser(),
                        $reservation->getService(),
                        $reservation->getStartDate(),
                        $reservation->getEndDate(),
                        $reservation->getDoctor(),
                    )
                );

                return $this->redirect(
                    $this->adminUrlGenerator
                        ->setDashboard(PatientDashboardController::class)
                        ->setController(self::class)
                        ->setRoute('app_patient_reservation')
                        ->generateUrl()
                );
            }

            $this->entityManager->persist($reservation);
            $this->entityManager->flush();

            return $this->redirect(
                $this->adminUrlGenerator
                    ->setDashboard(PatientDashboardController::class)
                    ->setController(self::class)
                    ->setRoute('app_patient_reservation')
                    ->generateUrl()
            );
        }

        return $this->render('patient-dashboard/reservation_calendar.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reservations', name: 'get_reservations', methods:"GET")]
    public function getReservations(): Response
    {
        $user = $this->getUser();
        $reservations = $this->reservationRepository->findBy(['user' => $user]);

        return $this->json (
            ['data' => $this->generateResponse($reservations)]
        );
    }

    /**
     * @throws \Exception
     */
    #[Route('/reservation/{id}', name: 'edit_reservation', methods:"PUT")]
    public function editReservation(Reservation $reservation, Request $request): Response
    {
        if ($this->isGranted(ReservationVoter::EDIT)) {
            return $this->json(['error' => true], Response::HTTP_FORBIDDEN);
        }

        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $startDateTime = new \DateTimeImmutable(trim($data['start']));
            $endDateTime = new \DateTimeImmutable(trim($data['end']));
        } catch (\JsonException | \Exception $exception) {
            return $this->json(['error' => true, 'message' => $exception], Response::HTTP_BAD_REQUEST);
        }

        $service = $this->getService($reservation, $data);

        if ($service === null) {
            return $this->json(['error' => true, 'message' => 'service not found'], Response::HTTP_BAD_REQUEST);
        }

        $reservation->setTitle(trim($data['title']));
        $reservation->setService($service);
        $reservation->setStartDate($startDateTime);
        $reservation->setEndDate($endDateTime);

        $this->entityManager->flush();


        return $this->json(['success' => true], Response::HTTP_ACCEPTED);
    }

    /**
     * @throws \Exception
     */
    #[Route('/reservation/{id}', name: 'delete_reservation', methods:"DELETE")]
    public function deleteReservation(Reservation $reservation): Response
    {
        if ($this->isGranted(ReservationVoter::EDIT)) {
            return $this->json(['error' => true], Response::HTTP_FORBIDDEN);
        }

        $this->entityManager->remove($reservation);
        $this->entityManager->flush();

        return $this->json(['success' => true], Response::HTTP_ACCEPTED);
    }

    private function generateResponse(array $reservations): array
    {
        $data = [];

        foreach ($reservations as $reservation) {
            if ($reservation->getStartDate() === null) {
                continue;
            }

            if ($reservation->getEndDate() === null) {
                continue;
            }

            if ($reservation->getService() === null) {
                continue;
            }

            $data[] = [
                'id' => $reservation->getId(),
                'title' => $reservation->getTitle(),
                'start' => $reservation->getStartDate()->format('Y-m-d\TH:i'),
                'end' => $reservation->getEndDate()->format('Y-m-d\TH:i'),
                'service' => $reservation->getService()->getId(),
                'doctor' => $reservation->getDoctor()->getId()
            ];
        }

        return $data;
    }

    private function getService(Reservation $reservation, array $data): ?Service
    {
        if ($data['service'] === '') {
            return null;
        }

        if ($reservation->getService() === null) {
            return null;
        }

        if ($reservation->getService()->getId() === $data['service']) {
            return $reservation->getService();
        }

        return $this->serviceRepository->findOneBy(['id' => $data['service']]);
    }

    private function getEndDate(Reservation $reservation): \DateTimeInterface
    {
        return $reservation->getStartDate()->modify('+' . $reservation->getService()->getDuration() . 'minutes');
    }
}
