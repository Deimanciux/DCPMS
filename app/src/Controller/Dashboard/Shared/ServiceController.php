<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Shared;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    #[Route('/service/doctor/{user}', name: 'get_services', methods:"GET")]
    public function getReservations(User $user): Response
    {
        $services = [];

        foreach($user->getServices() as $service) {
            $services[] = [
                'id' => $service->getService()->getId(),
                'title' => $service->getService()->getTitle()
            ];
        }

        return $this->json (
            ['data' => $services]
        );
    }
}
