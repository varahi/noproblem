<?php

namespace App\Controller\Payment;

use App\Entity\Order;
use App\Repository\TariffRepository;
use App\Service\SaveOrderService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Voronkovich\SberbankAcquiring\Client;
use Voronkovich\SberbankAcquiring\Currency;
use Voronkovich\SberbankAcquiring\HttpClient\HttpClientInterface as AcqHttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Voronkovich\SberbankAcquiring\OrderStatus;

class ProceedSber extends AbstractController
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
     * @param Security $security
     * @param ManagerRegistry $doctrine
     */
    public function __construct(
        Security $security,
        ManagerRegistry $doctrine
    ) {
        $this->security = $security;
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/pay/proceed_sber/{id}", name="payment_proceed_sber")
     * @return Response
     */
    public function proceedPayment(
        Request $request,
        string $id,
        TariffRepository $tariffRepository,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        SaveOrderService $saveOrderService
    ) {
        $client = new Client($this->acq_array_sber);
        $result = $client->execute('/payment/rest/getOrderStatusExtended.do', [
            'orderNumber' => $id,
        ]);

        if (OrderStatus::isDeposited($result['orderStatus'])) {
            $cookies = $request->cookies;
            if ($cookies->has('orderId')) {
                $orderId = $cookies->get('orderId');
                if ($orderId == $id) {
                    $tariffId = $result['orderDescription'];
                    $tariff = $tariffRepository->findOneBy(['id' => $tariffId]);
                    $user = $this->security->getUser();
                    $user->setisActive(true);
                    $saveOrderService->saveOrder($user, $tariff);

                    // Redirect to lk with message
                    $message = $translator->trans('Order was approved', array(), 'flash');
                    $notifier->send(new Notification($message, ['browser']));
                    return $this->redirectToRoute("app_lk");
                    //return $this->json(['data' => "Order was approved! and it's working!"]);
                }
            }
            //return $this->json(['data' => "Order was approved!"]);
            $message = $translator->trans('Order was approved', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_lk");
        } else {
            //return $this->json(['data' => "Order wasn't approved!"]);
            return $this->redirectToRoute("app_payment_error");
        }
    }
}
