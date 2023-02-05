<?php

namespace App\Controller\Payment;

use App\Repository\TariffRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Voronkovich\SberbankAcquiring\Client;
use Voronkovich\SberbankAcquiring\Currency;
use Voronkovich\SberbankAcquiring\HttpClient\HttpClientInterface as AcqHttpClientInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

class SberController extends AbstractController
{
    /**
     * @var array
     */
    private $acq_array_sber = [
        'userName' => 'T7736337091-api',
        'password' => 'T7736337091',
        'language' => 'ru',
        'currency' => Currency::RUB,
        'apiUri' => Client::API_URI_TEST,
        //'apiUri' => Client::API_URI,
        'httpMethod' => AcqHttpClientInterface::METHOD_GET,
    ];

    /**
     * @var Security
     */
    private $security;

    /**
     * @var string
     */
    private $defailtDomain;

    /**
     * @param Security $security
     * @param string $defailtDomain
     * @param ManagerRegistry $doctrine
     */
    public function __construct(
        Security $security,
        string $defailtDomain,
        ManagerRegistry $doctrine
    ) {
        $this->security = $security;
        $this->defailtDomain = $defailtDomain;
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/pay/sber", name="app_payment_sber")
     * @return Response
     */
    public function payment(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        TariffRepository $tariffRepository
    ): Response {
        $user = $this->security->getUser();
        if ((int)$user->getId() !== (int)$request->get('user')) {
            $message = $translator->trans('Access denied for this operation', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }

        $client = new Client($this->acq_array_sber);
        $orderId     = uniqid("", true);
        // // Required arguments
        // $cookies = $request->cookies;
        // if ($cookies->has('PHPSESSID')) {
        //     $orderId = $cookies->get('PHPSESSID');
        // }
        $tariff = $request->get('tariff');
        $tariff = $tariffRepository->findOneBy(['id' => $tariff]);
        $orderAmount = $tariff->getAmount();
        $returnUrl   = 'https://' . $this->defailtDomain . '/pay/proceed_sber/' . $orderId;

        // You can pass additional parameters like a currency code and etc.
        $params['currency'] = Currency::RUB;
        $params['description'] = $tariff->getId();
        $params['user'] = $user;

        $result = $client->registerOrder($orderId, $orderAmount, $returnUrl, $params);

        $response = new RedirectResponse($result['formUrl']);
        $cookie = new Cookie('orderId', $orderId, strtotime('now + 2 days'));
        $response->headers->setCookie($cookie);

        return $response;
    }
}
