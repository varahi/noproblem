<?php

declare(strict_types=1);

namespace App\Controller\Traits;

trait MapTrait
{
    /**
     * @param $items
     * @return mixed|null
     */
    public function getLngArr($items)
    {
        if (count($items) > 0) {
            foreach ($items as $item) {
                if ($item->getLongitude() !==null) {
                    $lngArr[] = $item->getLongitude();
                } else {
                    $lngArr[] = $item->getCity()->getLongitude();
                }
            }
            $lngArr = max($lngArr);
        } else {
            $lngArr = null;
        }

        return $lngArr;
    }

    /**
     * @param $items
     * @return mixed|null
     */
    public function getLatArr($items)
    {
        if (count($items) > 0) {
            foreach ($items as $item) {
                if ($item->getLatitude() !==null) {
                    $latArr[] = $item->getLatitude();
                } else {
                    $latArr[] = $item->getCity()->getLatitude();
                }
            }
            $latArr = min($latArr);
        } else {
            $latArr = null;
        }

        return $latArr;
    }
}
