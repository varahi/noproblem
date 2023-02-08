<?php

namespace App\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SupportController extends AbstractController
{
    /**
     * @Route("/support", name="app_support")
     */
    public function index(): Response
    {
        return $this->render('pages/support/index.html.twig', [
        ]);
    }
}
