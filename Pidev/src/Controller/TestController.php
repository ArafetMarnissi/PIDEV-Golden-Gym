<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('baseback.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

<<<<<<< HEAD
   /* #[Route('/test1', name: 'app_test')]
=======
    /* #[Route('/test1', name: 'app_test')]
>>>>>>> 97ebc60cafdf1a0cff1154faab316e13b3bb84d1
    public function index1(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }*/
}
