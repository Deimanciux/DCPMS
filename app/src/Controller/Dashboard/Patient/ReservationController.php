<?php

declare(strict_types = 1);

namespace App\Controller\Dashboard\Patient;

use App\Controller\Dashboard\PatientDashboardController;
use App\DTO\ReservationDTO;
use App\Entity\Reservation;
use App\Entity\Service;
use App\Entity\User;
use App\Entity\WorkSchedule;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\ServiceRepository;
use App\Repository\WorkScheduleRepository;
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
        private WorkScheduleRepository $workScheduleRepository
    ) {
    }

    #[Route('/reservations/{user}', name: 'reservation_by_user')]
    public function index(Request $request, User $user): Response
    {
        $form = $this->createForm(ReservationType::class, new Reservation(), [
            'action' => $this->generateUrl('reservation_by_user', ['user' => $user->getId()]),
        ]);

        $loggedUser = $this->getUser();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * @var Reservation
             */
            $reservation = $form->getData();
            $reservation->setStartDate(new \DateTimeImmutable( $form->get('startDate')->getData()->format('Y-m-d') . ' ' .  $form->get('startTime')->getData()->format('H:i')));
            $reservation->setEndDate($this->getEndDate($reservation));

            if ($reservation->getId() !== null) {

                $this->reservationUpdater->update(
                    new ReservationDTO(
                        $reservation->getId(),
                        $reservation->getReasonOfVisit(),
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
                        ->setRoute('reservation_by_user', ['user' => $user->getId()])
                        ->generateUrl()
                );
            }

            $this->entityManager->persist($reservation);
            $this->entityManager->flush();

            return $this->redirect(
                $this->adminUrlGenerator
                    ->setDashboard(PatientDashboardController::class)
                    ->setController(self::class)
                    ->setRoute('reservation_by_user', ['user' => $user->getId()])
                    ->generateUrl()
            );
        }

        $hoursByDays = [];
        if (\in_array(User::ROLE_DOCTOR, $this->getUser()->getRoles(), true)) {
            /**
             * @var WorkSchedule $workSchedule
             */
            foreach ($this->getUser()->getWorkSchedules() as $workSchedule) {
                $hoursByDays[$workSchedule->getWeekDay()] = [
                    'from' => $workSchedule->getWorkFrom()->format('H:i'),
                    'to' => $workSchedule->getWorkTo()->format('H:i'),
                ];
            }
        }

        return $this->render('dashboard/reservation_calendar.html.twig', [
            'form' => $form->createView(),
            'user' => $user->getId(),
            'patientUser' => $user,
            'hoursByDays' => $hoursByDays,
        ]);
    }

    #[Route('/reservations/user/{user}', name: 'get_reservations', methods:"GET")]
    public function getReservations(User $user): Response
    {
        if (in_array(User::ROLE_DOCTOR, $user->getRoles(), true)) {
            $reservations = $this->reservationRepository->findBy(['doctor' => $user]);
        } else {
            $reservations = $this->reservationRepository->findBy(['user' => $user]);
        }

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
//        if ($this->isGranted(ReservationVoter::EDIT)) {
//            return $this->json(['error' => true,], Response::HTTP_FORBIDDEN);
//        }

        $this->entityManager->remove($reservation);
        $this->entityManager->flush();

        return $this->json(['success' => true], Response::HTTP_ACCEPTED);
    }

    /**
     * @param Reservation[] $reservations
     */
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
                'title' => $reservation->getService()->getTitle(),
                'start' => $reservation->getStartDate()->format('Y-m-d\TH:i'),
                'end' => $reservation->getEndDate()->format('Y-m-d\TH:i'),
                'service' => $reservation->getService()->getId(),
                'doctor' => $reservation->getDoctor()->getId(),
                'patient' => $reservation->getUser()->getId(),
                'reasonOfVisit' => $reservation->getReasonOfVisit(),
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

    #[Route('/reservation/time/{date}/doctor/{doctor}/service/{service}', name: 'get_time_slots_reservation', methods:"GET")]
    public function getAvailableTimeSlots(\DateTimeImmutable $date, User $doctor, Service $service): Response
    {
        $weekDays = [
            1 => "Monday",
            2 => "Tuesday",
            3 => "Wednesday",
            4 => "Thursday",
            5 => "Friday",
            6 => "Saturday",
            7 => "Sunday",
        ];

        $dayOfWeek = date("l", $date->getTimestamp());
        $weekDayNumber = array_flip($weekDays)[$dayOfWeek];
        $workSchedule = $this->workScheduleRepository->findOneBy(['user' => $doctor, 'weekDay' => $weekDayNumber]);

        $workFrom = $workSchedule?->getWorkFrom($date) ?? new \DateTimeImmutable($date->format('Y-m-d') . ' 08:00');
        $workTo = $workSchedule?->getWorkTo($date) ?? new \DateTimeImmutable($date->format('Y-m-d') . ' 18:00');
        $reservations = $this->reservationRepository->getReservationsByDate($doctor, $date);
        $reservationCount = count($reservations);

        $availableTime = [];
        /**
         * @var Reservation $reservation
         */
        foreach ($reservations as $key => $reservation) {
            $availableTime[] = [
                'from' => $workFrom,
                'to' => ($reservationCount === $key + 1 && $reservationCount !== 1 ? $workTo : $reservation->getStartDate()),
                'duration' => $reservation->getService()->getDuration()
            ];
            $workFrom = $reservation->getStartDate()->modify('+'.$reservation->getService()->getDuration().' minutes');
        }

        if ($reservationCount === 0) {
            $availableTime[] = [
                'from' => $workFrom,
                'to'   => $workTo,
            ];
        } else {
            $lastAvailableTime = end($availableTime);
            if ($lastAvailableTime['to']->format('H:i') !== $workTo->format('H:i')) {
                $availableTime[] = [
                    'from' => $lastAvailableTime['to']->modify('+'.$lastAvailableTime['duration'].' minutes'),
                    'to'   => $workTo,
                ];
            }
        }

        return $this->json (
            ['data' => $this->getTimeSlots($availableTime, $service->getDuration())]
        );
    }

    public function getTimeSlots(array $availableTime, int $duration)
    {
        $slots = [];
        foreach ($availableTime as $time) {
            while($time['from']->modify('+'.$duration.' minutes') <= $time['to']) {
                $slots[] = $time['from']->format('H:i');
                $time['from'] = $time['from']->modify('+'.$duration.' minutes');
            }
        }

        return $slots;
    }
}
