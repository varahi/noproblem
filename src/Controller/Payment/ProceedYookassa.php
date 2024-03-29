<?php

namespace App\Controller\Payment;

use App\Repository\TariffRepository;
use App\Repository\UserRepository;
use App\Service\SaveOrderService;
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
     * @Route("/pay/proceed_yookassa/{id}", name="payment_proceed_yookassa")
     * @return Response
     */
    public function proceedPayment(
        Request $request,
        string $id,
        TariffRepository $tariffRepository,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        SaveOrderService $saveOrderService
    ): Response {
        $client = new YooClient();
        $client->setAuth($this->acq_array_yookassa['shopId'], $this->acq_array_yookassa['secretKey']);
        $cookies = $request->cookies;
        if (!$cookies->has('paymentId')) {
            return $this->redirectToRoute("app_payment_error");
        }
        $paymentId = $cookies->get('paymentId');

        try {
            $response = $client->getPaymentInfo($paymentId);
        } catch (\Exception $e) {
            $response = $e;
        }

        //ToDo: is this condition correct?
        //if ($cookies->get('paymentId') == $client->getPaymentInfo($paymentId)->getId()) {
        if ($response->getStatus() == 'succeeded') {
            $tariff = $tariffRepository->findOneBy(['id' => $response->getMetadata()['tariff']]);
            $user = $this->security->getUser();
            $user->setisActive(true);
            $saveOrderService->saveOrder($user, $tariff);
        }
        //}

        // Redirect to lk with message
        $message = $translator->trans('Order was approved', array(), 'flash');
        $notifier->send(new Notification($message, ['browser']));
        return $this->redirectToRoute("app_lk");
        //return $this->json(['data' => "Order was approved! and it's working!"]);
    }

    /**
     * @Route("/pay/yookassa_hooks", name="payment_hook_yookassa")
     * @return Response
     */
    public function hooks(
        Request $request,
        TariffRepository $tariffRepository,
        UserRepository $userRepository,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        SaveOrderService $saveOrderService
    ): Response {
        $client = new YooClient();
        $client->setAuth($this->acq_array_yookassa['shopId'], $this->acq_array_yookassa['secretKey']);

        $hook_data = json_decode($request->getContent(), true);
        if ($hook_data['event'] != 'payment.succeeded') {
            return $this->json(['data' => 'we dont proceed this type of event yet']);
        }
        $paymentId = $hook_data['object']['id'];

        try {
            $response = $client->getPaymentInfo($paymentId);
        } catch (\Exception $e) {
            return $this->json(['data' => 'no payment data found']);
        }


        if ($response->getStatus() == 'succeeded') {
            $tariff = $tariffRepository->findOneBy(['id' => $response->getMetadata()['tariff']]);
            $user = $userRepository->findOneBy(['id' => $response->getMetadata()['userId']]);
            $saveOrderService->saveOrder($user, $tariff);
        }

        return $this->json(['data' => 'success']);
    }
}
