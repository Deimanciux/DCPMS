<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
//    #[IsGranted('IS_AUTHENTICATED_FULLY')]
//    #[Route('/', name: 'app_index')]
//    public function index(): Response
//    {
//        return $this->render('index/landing.html.twig');
//    }
}
