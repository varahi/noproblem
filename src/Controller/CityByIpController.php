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
//    public $token = "62ae4a81a51849da1013da68f2ff86d8938292af";
//
//    public $secret = "6d240b631a8301e11edfdc12ad0ad27ef1a84a70";
//
//    public function __construct(
//        string $token,
//        string $secret
//    ) {
//        $this->token = $token;
//        $this->secret = $secret;
//    }

    /**
     * @Route("/city-by-ip", name="app_city_by_ip")
     */
    public function index(): Response
    {
        $dadata = new Dadata('62ae4a81a51849da1013da68f2ff86d8938292af', '6d240b631a8301e11edfdc12ad0ad27ef1a84a70');
        $dadata->init();

        //$result = $dadata->iplocate($_SERVER['REMOTE_ADDR']);
        $result = $dadata->iplocate('95.27.197.131');

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
    public function setCityByIp(
        Request $request,
        CityRepository $cityRepository
    ): JsonResponse {
        if ($request) {
            $session = new Session();

            // Получаем из POST массива название города, здесь в примере это переменная cityName
            //$city = $cityRepository->findOneBy(['name' => $cityName]);
            //$session->set('city', $city->getId());

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
