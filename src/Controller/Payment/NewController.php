<?php

namespace App\Controller\Payment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class NewController extends AbstractController
{
    /**
     * @param Security $security
     */
    public function __construct(
        Security $security
    ) {
        $this->security = $security;
    }

    /**
     * @Route("/pay/choose-bank", name="app_choose_bank")
     * @return Response
     */
    public function newPayment(
        Request $request
    ): Response {
        return $this->render('pages/payment/choose_bank.html.twig', [
            'tariff' => $request->query->get('tariff'),
            'user' => $this->security->getUser()
        ]);
    }
}
