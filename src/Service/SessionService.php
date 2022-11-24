<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Entity\City;
use App\Repository\CityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;

class SessionService extends AbstractController
{
    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(
        ManagerRegistry $doctrine
    ) {
        $this->doctrine = $doctrine;
    }

    public function getCity()
    {
        $session = new Session();
        $cityId = $session->get('city');
        $entityManager = $this->doctrine->getManager();
        //$city = $cityRepository->findOneBy(['id' => $cityId]);
        $city = $entityManager->getRepository(City::class)->findOneBy(['id' => $cityId]);
        return $city->getName();
    }
}
