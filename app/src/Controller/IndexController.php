<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    public function __construct(
        private ServiceRepository $service
    ){
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/index', name: 'app_index')]
    public function index(): Response
    {
        $services = $this->service->findActiveServices();
        $result = [];
        foreach ($services as $service)
        {

            $result = $service->getServiceImages();
        }

        dd($result);

        foreach ($result as $item)
        {
            dd($item->getId());
        }

        return $this->render('index/index.html.twig');
    }
}
