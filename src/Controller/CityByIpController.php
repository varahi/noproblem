<?php

namespace App\Controller;

use App\Repository\CityRepository;
use App\Service\Dadata;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class CityByIpController extends AbstractController
{
//    private $dadataToken;
//
//    private $dadataSecret;
//
//    public function __construct(
//        string $dadataToken,
//        string $dadataSecret
//    )
//    {
//        $this->dadataToken = $dadataToken;
//        $this->dadataSecret = $dadataSecret;
//    }

    /**
     * @Route("/city-by-ip", name="app_city_by_ip")
     */
    public function getCityByIp(): Response
    {
        $dadata = new Dadata('62ae4a81a51849da1013da68f2ff86d8938292af', '6d240b631a8301e11edfdc12ad0ad27ef1a84a70');
        //$dadata = new Dadata($this->dadataToken, $this->dadataSecret);
        $dadata->init();

        $result = $dadata->iplocate($_SERVER['REMOTE_ADDR']);
        //$result = $dadata->iplocate('95.27.197.131');

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode($result['location']['data']['city']));

        $dadata->close();
        return $response;
    }

    /**
     * @Route("/set-city-by-ip", name="app_set_city_by_ip")
     */
    public function setCity(
        Request $request,
        CityRepository $cityRepository
    ): JsonResponse {
        if ($request) {

            // Получаем из POST массива название города
            $city = $cityRepository->findOneBy(['name' => $request->get('city')]);
            $session = new Session();
            $session->set('city', $city->getName());

            return new JsonResponse([
                'status' => Response::HTTP_OK,
            ]);
        } else {
            return new JsonResponse([
                'status' => Response::HTTP_BAD_REQUEST,
            ]);
        }
    }
}
