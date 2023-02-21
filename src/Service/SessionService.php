<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\City;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RequestStack;

class SessionService
{
    /**
     * @param ManagerRegistry $doctrine
     */
    public function __construct(
        ManagerRegistry $doctrine,
        RequestStack $requestStack
    ) {
        $this->doctrine = $doctrine;
        $this->requestStack = $requestStack;
    }

    public function getCity()
    {
        if ($this->requestStack->getSession()) {
            $session = $this->requestStack->getSession();
        } else {
            $session = new Session();
        }

        $cityName = $session->get('city');
        $entityManager = $this->doctrine->getManager();
        $city = $entityManager->getRepository(City::class)->findOneBy(['name' => $cityName]);

        if ($city !==null) {
            return $city->getName();
        } else {
            return null;
        }
    }
}
