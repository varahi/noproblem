<?php

namespace App\Controller;

use App\Controller\Traits\AbstractTrait;
use App\Controller\Traits\DataTrait;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\ArticleCategoryRepository;
use App\Repository\CityRepository;
use App\Repository\CourseRepository;
use App\Repository\JobRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\Session;

class ApiController extends AbstractController
{
    use DataTrait;

    use AbstractTrait;

    private $security;

    public function __construct(
        Security $security
    ) {
        $this->security = $security;
    }

    /**
     * @Route("/api", name="app_api")
     */
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    /**
     * @Route("/api/courses", name="api_courses")
     * @return Response
     */
    public function apiCourses(
        CourseRepository $courseRepository
    ) {
        $items = $courseRepository->findLimitOrder('999', '0');
        $arrData = $this->getCourseJsonArrData($items);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode($arrData));

        return $response;
    }

    /**
     * @Route("/api/announcements", name="api_announcements")
     * @return Response
     */
    public function apiAnnouncements(
        JobRepository $jobRepository
    ) {
        $user = $this->security->getUser();
        if (isset($user) && $user !==null) {
            $jobs = $jobRepository->findByUser($user->getId());
            $arrData = $this->getAnnouncementsJsonArrData($jobs);
        } else {
            $arrData = [];
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode($arrData));
        return $response;
    }

    /**
     * @Route("/api/articles", name="api_articles")
     * @return Response
     */
    public function apiArticles(
        ArticleRepository $articleRepository
    ) {
        //$items = $articleRepository->findAllOrder(['id' => 'ASC']);
        $items = $articleRepository->findLimitOrder('999', '0');
        $arrData = $this->getArticleJsonArrData($items);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode($arrData));

        return $response;
    }

    /**
     * @Route("/api/article-{id}", name="api_detail_article")
     * @return Response
     */
    public function apiDetailArticle(
        ArticleRepository $articleRepository
    ) {
        $items = $articleRepository->findAllOrder(['id' => 'ASC']);
        $arrData = $this->getArticleJsonArrData($items);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode($arrData));

        return $response;
    }

    /**
     * @Route("/api/categories", name="api_categories")
     * @return Response
     */
    public function apiCategories(
        CategoryRepository $categoryRepository
    ) {
        //$items = $categoryRepository->findAllOrder(['id' => 'ASC']);
        $items = $categoryRepository->findLimitOrder('4', '0');
        $arrData = $this->getJsonArrData($items);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode($arrData));

        return $response;
    }

    /**
     * @Route("/api/blog-categories", name="api_blog_categories")
     * @return Response
     */
    public function apiBlogCategories(
        ArticleCategoryRepository $articleCategoryRepository
    ) {
        $items = $articleCategoryRepository->findAllOrder(['id' => 'ASC']);
        $arrData = $this->getJsonArrData($items);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode($arrData));

        return $response;
    }


    /**
     * @Route("/api/cities", name="api_cities")
     * @return Response
     */
    public function apiCities()
    {
        if (isset($_POST['name'])) {
            $searchword = $_POST['name'];
            $char = mb_strtoupper(substr($searchword, 0, 3), "utf-8"); // первый символ в верхний регистр
            $searchword[0] = $char[0];
            $searchword[1] = $char[1];
        } else {
            echo \json_encode('Город не найден');
        }

        $jsonFile = $this->getDomain() . '/data/russian-cities.json';
        $content = file_get_contents($jsonFile);

        if ($content == '') {
            $cities = [];
        } else {
            $cities = \json_decode($content);
        }

        foreach ($cities as $item) {
            if (stripos($item->name, $searchword) === 0) {
                $city[] = [
                    'name' => $item->name,
                    'lat' => $item->coords->lat,
                    'lng' => $item->coords->lon,
                    'district' => $item->district
                ];
            }
        }

        $_POST['maxRows'] = '5';

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        if (count($city) > 0) {
            if (count($city) > $_POST['maxRows']) {
                $city = array_slice($city, 0, $_POST['maxRows']);
            }
            $response->setContent(\json_encode($city));
        } else {
            $err[] = array('name'=>'Город не найден');
            $response->setContent(\json_encode($err));
        }

        return $response;
    }

    /**
     * @Route("/api/get-cities", name="api_get_cities")
     * @return Response
     */
    public function apiGetCities(
        CityRepository $cityRepository
    ) {
        //$items = $cityRepository->findAllOrder(['name' => 'ASC']);
        $items = $cityRepository->findLimitOrder('9999', '0');

        if ($items) {
            foreach ($items as $item) {
                if ($item->getId()) {
                    $itemId = $item->getId();
                }
                if ($item->getName()) {
                    $itemTitle = $item->getName();
                } else {
                    $itemTitle = null;
                }

                $arrData[] = [
                    'id' => $itemId,
                    'title' => $itemTitle,
                ];
            }
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode($arrData));

        return $response;
    }

    /**
     * @Route("/api/set-city", name="app_set_city")
     */
    public function setCity(Request $request): JsonResponse
    {
        if ($request) {
            $data = json_decode($request->getContent(), true);
            $cityId = $data['cname'];
            //file_put_contents('data.txt', $cityId);

            $session = new Session();
            $session->set('city', $cityId);
            //$citySession = $session->get('city');

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
