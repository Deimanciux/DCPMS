<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Shared;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/roles/{user}', name: 'get_roles', methods:"GET")]
    public function getReservations(User $user): Response
    {
        return $this->json (
            ['data' => $user->getRoles()]
        );
    }
}
