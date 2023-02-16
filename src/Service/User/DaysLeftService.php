<?php

declare(strict_types=1);

namespace App\Service\User;

class DaysLeftService
{
    public const WELCOME_ACCESS_1 = '1';

    public const WELCOME_ACCESS_2 = '7';

    /**
     * @param $order
     * @param $user
     * @return null
     * @throws \Exception
     */
    public function getDaysLeft($order, $user)
    {
        if ($order == null) {
            return null;
        }

        if ($order !==null) {
            $currentDateStr = date('Y-m-d H:i:s');
            $currentDate = new \DateTime($currentDateStr);
            if ($order->getEndDate() !==null) {
                $daysLeft = $order->getEndDate()->diff($currentDate)->format("%a");
            }
            if (isset($daysLeft) && $daysLeft <= 0) {
                $user->setIsActive(false);
            } else {
                $user->setIsActive(true);
            }

            if ($order->getTariff()->getId() == self::WELCOME_ACCESS_1 || $order->getTariff()->getId() == self::WELCOME_ACCESS_2) {
                return $daysLeft + 1;
            }
        }

        return $daysLeft;
    }
}
