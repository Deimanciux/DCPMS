<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Service;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    #[Route('/service/doctors/{id}', name: 'get_service_doctors', methods:"GET")]
    public function getReservations(Service $service): Response
    {
        $mappedUsers = $this->mapUsersResponse($service->getUsers());

        return $this->json (
            ['data' => $mappedUsers]
        );
    }

    private function mapUsersResponse(Collection $userServices): array
    {
        $result = [];

        foreach ($userServices as $userService) {
            $result[] = [
                'id' =>  $userService->getUser()->getId(),
                'name_surname' => sprintf('%s %s', $userService->getUser()->getName(), $userService->getUser()->getSurname())
            ];
        }

        return $result;
    }
}
