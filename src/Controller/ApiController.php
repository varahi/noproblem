<?php

namespace App\Controller;

use App\Controller\Traits\DataTrait;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    use DataTrait;

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
     * @Route("/api/articles", name="api_articles")
     * @return Response
     */
    public function apiArticles(
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
        $items = $categoryRepository->findAllOrder(['id' => 'ASC']);
        $arrData = $this->getJsonArrData($items);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode($arrData));

        return $response;
    }
}
