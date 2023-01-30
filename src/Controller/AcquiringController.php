<?php

namespace App\Controller;

use App\Controller\Traits\AbstractTrait;
use App\Entity\Order;
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
use YooKassa\Client as YooClient;

class AcquiringController extends AbstractController
{
    use AbstractTrait;

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

    private $acq_array_yookassa = [
        'shopId' => '949967',
        'secretKey' => 'live_Lpu1u-LVIpOk-yXy5NVOQIdCx1XGyei9FVC5-Lpxukk'
    ];

    /**
     * @var Security
     */
    private $security;

    /**
     * @var string
     */
    private $defailtDomain;

    public const YOOKASSA_METHODS = [
        'bank_card',
        'sberbank',
        'yoo_money',
        'alfabank',
        'tinkoff_bank',
        'qiwi',
        'cash'
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
     * @Route("/pay/new", name="payment_new")
     * @return Response
     */
    public function newPayment(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        TariffRepository $tariffRepository
    ) {
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // $user = $this->getUser();
        $user = $this->security->getUser();
        if ((int)$user->getId() !== (int)$request->get('user')) {
            $message = $translator->trans('Access denied for this operation', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }

        // выбрать тип оплаты:
        $bank = $request->get('bank') ?? '';

        if ($bank == 'sber') {
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
        } elseif ($bank == 'yookassa') {
            $method = $request->get('method') ?? '';
            if (in_array($method, self::YOOKASSA_METHODS)) {
                $client = new YooClient();
                $client->setAuth($this->acq_array_yookassa['shopId'], $this->acq_array_yookassa['secretKey']);

                $orderId     = uniqid("", true);
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

                $confirmationUrl = $response->getConfirmation()->getConfirmationUrl();

                $response = $this->redirect($confirmationUrl);
                $cookie = new Cookie('orderId', $orderId, strtotime('now + 2 days'));
                $response->headers->setCookie($cookie);

                return $response;
            } else {
                return $this->render('pages/yookassa_widget.html.twig');
            }
        } else {
            return $this->render('pages/choose_bank.html.twig', []);
        }
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
        NotifierInterface $notifier
    ) {
        $client = new Client($this->acq_array_sber);
        $entityManager = $this->doctrine->getManager();

        $result = $client->execute('/payment/rest/getOrderStatusExtended.do', [
            'orderNumber' => $id,
        ]);

        if (OrderStatus::isDeposited($result['orderStatus'])) {
            $cookies = $request->cookies;
            if ($cookies->has('orderId')) {
                $orderId = $cookies->get('orderId');
                if ($orderId == $id) {

                    // Logic of succeed
                    // Создаем новый заказ
                    $user = $this->security->getUser();
                    // Необходимо получить id тарифа и найти его в БД
                    $tariff = $result['orderDescription'];
                    $tariff = $tariffRepository->findOneBy(['id' => $tariff]);
                    $order = new Order();
                    $order->setName('Order #' . 'User ID - ' . $user->getId() . ' Tariff ID - ' . $tariff->getId());
                    $order->setUser($user);
                    $order->setActive(1);
                    //$order->setEndDate()
                    $order->setTariff($tariff);
                    $user->setActive(true);

                    // We need to save data here to get order created date
                    //$entityManager->persist($order);
                    //$entityManager->flush();

                    $currentDateStr = date('Y-m-d H:i:s');
                    $currentDate = new \DateTime($currentDateStr);
                    $interval = '+' . $tariff->getTermDays() . 'day';
                    $endDate = $currentDate->modify($interval);
                    $order->setEndDate($endDate);

                    // Flush data again
                    $entityManager->persist($order);
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $order->setTariff($tariff);
                    $entityManager->persist($order);

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

    /**
     * @Route("/pay/proceed_yookassa/{id}", name="payment_webhook_yookassa")
     * @return Response
     */
    public function proceedWebhookYookassa(
        Request $request,
        string $id,
        TariffRepository $tariffRepository,
        TranslatorInterface $translator,
        NotifierInterface $notifier
    ) {
        $entityManager = $this->doctrine->getManager();
        if ($request->getMethod() != 'POST') {
            return $this->json(['data' => 'prohobited']);
        }
        $content = $request->getContent();

        $client = new YooClient();
        $client->setAuth($this->acq_array_yookassa['shopId'], $this->acq_array_yookassa['secretKey']);

        $paymentId = '215d8da0-000f-50be-b000-0003308c89be';
        try {
            $response = $client->getPaymentInfo($paymentId);
        } catch (\Exception $e) {
            $response = $e;
        }
        if ($response['paid']) {
            $cookies = $request->cookies;
            if ($cookies->has('orderId')) {
                $orderId = $cookies->get('orderId');
                if ($orderId == $id) {

                    // Logic of succeed
                    // Создаем новый заказ
                    $user = $this->security->getUser();
                    // Необходимо получить id тарифа и найти его в БД
                    //$tariff = $result['orderDescription'];
                    $tariff = $request->get('tariff');
                    $tariff = $tariffRepository->findOneBy(['id' => $tariff]);
                    $order = new Order();
                    $order->setName('Order #' . 'User ID - ' . $user->getId() . ' Tariff ID - ' . $tariff->getId());
                    $order->setUser($user);
                    $order->setActive(1);
                    //$order->setEndDate()
                    $order->setTariff($tariff);

                    // We need to save data here to get order created date
                    //$entityManager->persist($order);
                    //$entityManager->flush();

                    $currentDateStr = date('Y-m-d H:i:s');
                    $currentDate = new \DateTime($currentDateStr);
                    $interval = '+' . $tariff->getTermDays() . 'day';
                    $endDate = $currentDate->modify($interval);
                    $order->setEndDate($endDate);

                    // Flush data again
                    $entityManager->persist($order);
                    $entityManager->flush();

                    $order->setTariff($tariff);
                    $entityManager->persist($order);

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
