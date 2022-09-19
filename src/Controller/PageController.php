<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Traits\DataTrait;

class PageController extends AbstractController
{

    use DataTrait;

    /**
     * @Route("/", name="app_home")
     */
    public function home()
    {
        return $this->render('page/home.html.twig', [
        ]);
    }

    /**
     * @Route("/articles", name="app_articles")
     */
    public function articles()
    {
        return $this->render('articles/index.html.twig', [
        ]);
    }

    /**
     * @Route("/api/articles", name="api_articles")
     * @return Response
     */
    public function getArticles(
        ArticleRepository $articleRepository
    ) {
        $items = $articleRepository->findAllOrder(['id' => 'ASC']);
        $arrData = $this->getJsonArrData($items);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->setContent(json_encode($arrData));

        return $response;
    }
}
