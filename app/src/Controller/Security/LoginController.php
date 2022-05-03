<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('patient_dashboard');
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        $form = $this->createForm(
            LoginFormType::class,
        );

        return $this->renderForm('security/login.html.twig', [
            'form' => $form,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void {}
}
