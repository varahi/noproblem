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
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use YooKassa\Client as YooClient;

class YookassaController extends AbstractController
{
    private $acq_array_yookassa = [
        'shopId' => '949967',
        'secretKey' => 'live_Lpu1u-LVIpOk-yXy5NVOQIdCx1XGyei9FVC5-Lpxukk'
    ];

    public const YOOKASSA_METHODS = [
        'bank_card',
        'sberbank',
        'yoo_money',
        'alfabank',
        'tinkoff_bank',
        'qiwi',
        'cash',
        'sbp'
    ];

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
     * @Route("/pay/yookassa", name="app_payment_yookassa")
     * @return Response
     */
    public function choosePayment(
        Request $request
    ) {
        return $this->render('pages/payment/yookassa_widget.html.twig', [
            'tariff' => $request->query->get('tariff'),
            'user' => $this->security->getUser()
        ]);
    }

    /**
     * @Route("/new-pay/yookassa", name="app_new_pay_yookassa")
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

        $method = $request->get('method') ?? '';
        if (!in_array($method, self::YOOKASSA_METHODS)) {
            return $this->render('pages/payment/yookassa_widget.html.twig');
        }

        $client = new YooClient();
        $client->setAuth($this->acq_array_yookassa['shopId'], $this->acq_array_yookassa['secretKey']);

        $orderId = uniqid("", true);
        $tariff = $request->get('tariff');
        $tariff = $tariffRepository->findOneBy(['id' => $tariff]);
        $orderAmount = $tariff->getAmount() / 100;
        $returnUrl   = 'https://' . $this->defailtDomain . '/pay/proceed_yookassa/' . $orderId;

        $idempotenceKey = uniqid('', true);
        $response = $client->createPayment(
            [
                'amount' => [
                    'value' => $orderAmount,
                    'currency' => 'RUB',
                ],
                'capture' => true,
                'description' => $tariff->getId(),
                'metadata' => [
                    'orderNumber' => $orderId
                ],
                "confirmation" => [
                    "type" => "redirect",
                    'return_url' => $returnUrl,
                ],
                'payment_method_data' => array(
                    'type' => $method,
                ),
            ],
            $idempotenceKey
        );
        $paymentId = $response->getId();

        $confirmationUrl = $response->getConfirmation()->getConfirmationUrl();

        $response = $this->redirect($confirmationUrl);
        $cookie = new Cookie('orderId', $orderId, strtotime('now + 2 days'));
        $response->headers->setCookie($cookie);
        $cookie = new Cookie('paymentId', $paymentId, strtotime('now + 2 days'));
        $response->headers->setCookie($cookie);

        return $response;
    }
}
