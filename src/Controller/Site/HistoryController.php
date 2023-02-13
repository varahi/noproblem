<?php

namespace App\Controller\Site;

use App\Repository\OrderRepository;
use App\Service\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class HistoryController extends AbstractController
{
    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';

    public function __construct(
        Security $security
    ) {
        $this->security = $security;
    }

    /**
     * @Route("/payment-history", name="app_history")
     */
    public function index(
        OrderRepository $orderRepository,
        TranslatorInterface $translator,
        NotifierInterface $notifier
    ): Response {
        $user = $this->security->getUser();
        if (!$user) {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }

        return $this->render('pages/history/index.html.twig', [
            'orders' => $orderRepository->findByUser($user->getId())
        ]);
    }

    /**
     * @Route("/download-payment-history", name="app_download_history")
     */
    public function downloadHistory(
        FileService $fileService,
        OrderRepository $orderRepository
    ): Response {
        $user = $this->security->getUser();
        $orders = $orderRepository->findByUser($user->getId());

        $fileName = 'payment-history.csv';
        $headline = 'Order Number;Phone Number;Payment Date;Sum';
        $this->saveHistoryCSVFile($orders, $fileName, $headline, $fileService);
        $fileService->downloadFile($fileName);

        $response = new Response();
        return $response;
    }

    /**
     * @param $orders
     * @param $filePathName
     * @param $headline
     * @param FileService $fileService
     * @return void
     */
    private function saveHistoryCSVFile($orders, $filePathName, $headline, FileService $fileService)
    {
        $fileService->writeCSVFileEntry($filePathName, $headline."\r\n", 'w+');
        $tableColumns = explode(';', $headline);

        if ($orders) {
            foreach ($orders as $order) {
                $record = '';

                foreach ($tableColumns as $column) {
                    switch ($column) {
                        case 'Order Number':
                            $record .= $order->getId().';';
                            break;
                        case 'Phone Number':
                            $record .= $order->getUser()->getPhone().';';
                            break;
                        case 'Payment Date':
                            $record .= $order->getCreated()->format('Y-m-d H:i:s').';';
                            break;
                        case 'Sum':
                            $sum = $order->getTariff()->getAmount()/100 . ' Rub';
                            $record .= $sum.';';
                            break;
                        default:
                            $record .= ';';
                    }
                }
                $fileService->writeCSVFileEntry($filePathName, $record."\r\n", 'a+');
            }
        }
    }
}
