<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Order;
use App\Entity\Tariff;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;

class SaveOrderService extends AbstractController
{
    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(
        ManagerRegistry $doctrine
    ) {
        $this->doctrine = $doctrine;
    }

    public function saveOrder(
        User $user,
        Tariff $tariff
    ) {
        $entityManager = $this->doctrine->getManager();

        $order = new Order();
        $order->setName('Order #' . 'User ID - ' . $user->getId() . ' Tariff ID - ' . $tariff->getId());
        $order->setUser($user);
        $order->setActive(true);
        $order->setTariff($tariff);

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
    }
}
