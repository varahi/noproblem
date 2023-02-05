<?php

namespace App\Controller\Payment;

use App\Entity\Order;
use App\Repository\TariffRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;
use YooKassa\Client as YooClient;

class ProceedYookassa extends AbstractController
{
    private $acq_array_yookassa = [
        'shopId' => '949967',
        'secretKey' => 'live_Lpu1u-LVIpOk-yXy5NVOQIdCx1XGyei9FVC5-Lpxukk'
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
     * @Route("/pay/proceed_yookassa/{id}", name="payment_proceed_sber")
     * @return Response
     */
    public function proceedPayment(
        Request $request,
        string $id,
        TariffRepository $tariffRepository,
        TranslatorInterface $translator,
        NotifierInterface $notifier
    ): Response {
        $client = new YooClient();
        $client->setAuth($this->acq_array_yookassa['shopId'], $this->acq_array_yookassa['secretKey']);
        $entityManager = $this->doctrine->getManager();

        $cookies = $request->cookies;
        if (!$cookies->has('paymentId')) {
            return $this->redirectToRoute("app_payment_error");
        }
        $paymentId = $cookies->get('paymentId');
        $paymentInfo = $client->getPaymentInfo($paymentId);

        if ($paymentInfo->getStatus() != "succeeded") {
            //return $this->json(['data' => "Order wasn't approved!"]);
            return $this->redirectToRoute("app_tarifs");
        }

        // Logic of succeed
        // Создаем новый заказ
        $user = $this->security->getUser();
        // Необходимо получить id тарифа и найти его в БД
        $tariff = $paymentInfo['description'];
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
