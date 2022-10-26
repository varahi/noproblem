<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Tariff;
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
use Voronkovich\SberbankAcquiring\OrderStatus;

class AcquiringController extends AbstractController
{
    private $acq_array = [
        'userName' => 'T7736337091-api',
        'password' => 'T7736337091',
        'language' => 'ru',
        'currency' => Currency::RUB,
        'apiUri' => Client::API_URI_TEST,
        'httpMethod' => AcqHttpClientInterface::METHOD_GET,
    ];

    private $security;

    private $defailtDomain;

    public function __construct(
        Security $security,
        string $defailtDomain,
        ManagerRegistry $doctrine
    ) {
        $this->security = $security;
        $this->defailtDomain = $defailtDomain;
        $this->doctrine = $doctrine;
    }

    // private $security;

    // public function __construct(
    //     Security $security
    // ) {
    //     $this->security = $security;
    // }

    /**
     * @Route("/pay/new", name="payment_new")
     * @return Response
     */
    public function newPayment(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        TariffRepository $tariffRepository
    ) {
        // TODO: fill token, domain

        $user = $this->security->getUser();
        if ((int)$user->getId() !== (int)$request->get('user')) {
            $message = $translator->trans('Access denied for this operation', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }

        $client = new Client($this->acq_array);
        $orderId     = uniqid("", true);
        // // Required arguments
        // $cookies = $request->cookies;
        // if ($cookies->has('PHPSESSID')) {
        //     $orderId = $cookies->get('PHPSESSID');
        // }
        
        $tariff = $tariffRepository->findOneBy(['id' => $tariff]);
        $orderAmount = $tariff->getAmount();
        $returnUrl   = 'https://'.$this->defailtDomain.'/pay/proceed/'.$orderId;

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

    /**
     * @Route("/pay/proceed/{id}", name="payment_proceed")
     * @return Response
     */
    public function proceedPayment(
        Request $request,
        string $id,
        TariffRepository $tariffRepository
    ){
        $client = new Client($this->acq_array);
        $entityManager = $this->doctrine->getManager();

        $result = $client->execute('/payment/rest/getOrderStatusExtended.do', [
            'orderNumber' => $id,
        ]);

        if (OrderStatus::isApproved($result['orderStatus'])) {
            $cookies = $request->cookies;
            if ($cookies->has('orderId')) {
                $orderId = $cookies->get('orderId');
                if ($orderId == $id) {

                    // TODO: logic of succeed
                    // Создаем новый заказ
                    $user = $this->security->getUser();
                    // Необходимо получить id тарифа и найти его в БД
                    $tariff = $result['orderDescription'];
                    $tariff = $tariffRepository->findOneBy(['id' => $tariff]);
                    $order = new Order();
                    $order->setName('Order #' .'User ID - '. $user->getId() .' Tariff ID - '. $tariff->getName());
                    $order->setUser($user);
                    $order->setTariff($tariff);
                    $entityManager->persist($order);
                    $entityManager->flush();

                    return $this->json(['data' => "Order was approved! and it's working!"]);
                }
            }
            return $this->json(['data' => "Order was approved!"]);
        } else {
            return $this->json(['data' => "Order wasn't approved!"]);
        }
    }
}
