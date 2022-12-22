<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;

class CoordinateService extends AbstractController
{
    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(
        ManagerRegistry $doctrine
    ) {
        $this->doctrine = $doctrine;
    }

    public function setCoordinates($item)
    {
        // Serach by address
        $address = str_replace(" ", "+", $item->getAddress());
        if ($item->getCity()) {
            $urlRequest = 'https://nominatim.openstreetmap.org/search.php?q='.$address.'+'.$item->getCity()->getName().'&countrycodes=ru&limit=1&format=jsonv2';
        } else {
            $urlRequest = 'https://nominatim.openstreetmap.org/search.php?q='.$address.'+'.'&countrycodes=ru&limit=1&format=jsonv2';
        }

        // Create a stream
        $opts = array('http'=>array('header'=>"User-Agent: StevesCleverAddressScript 3.7.6\r\n"));
        $context = stream_context_create($opts);

        // Open the file using the HTTP headers set above
        $data = file_get_contents($urlRequest, false, $context);

        $json = json_decode($data);
        foreach ($json as $data) {
            $lat = $data->lat;
            $lon = $data->lon;
        }

        if (isset($lat) && isset($lon)) {
            $item->setLatitude($lat);
            $item->setLongitude($lon);
        } else {
            $item->setLatitude($item->getCity()->getLatitude());
            $item->setLongitude($item->getCity()->getLongitude());
        }

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($item);
        $entityManager->flush();
    }

    /**
     * @param $items
     * @param $city
     * @return mixed|null
     */
    public function getLngArr($items, $city = '')
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
            if ($city) {
                $lngArr = $city->getLongitude();
            } else {
                $lngArr = null;
            }
        }

        return $lngArr;
    }

    /**
     * @param $items
     * @param $city
     * @return mixed|null
     */
    public function getLatArr($items, $city = '')
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
            if ($city) {
                $latArr = $city->getLatitude();
            } else {
                $latArr = null;
            }
        }

        return $latArr;
    }
}
