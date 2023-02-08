<?php

namespace App\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoryController extends AbstractController
{
    /**
     * @Route("/payment-history", name="app_history")
     */
    public function index(): Response
    {
        return $this->render('pages/history/index.html.twig', [
            'controller_name' => 'HistoryController',
        ]);
    }
}
