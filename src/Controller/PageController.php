<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\City;
use App\Repository\ArticleRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Traits\DataTrait;
use App\Form\MessageFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Security;

class PageController extends AbstractController
{
    use DataTrait;

    /**
     * @Route("/", name="app_main")
     */
    public function mainPage(
        Request $request,
        ReviewRepository $reviewRepository
    ): Response {
        $message = new Message();
        $form = $this->createForm(MessageFormType::class, $message);
        $form->handleRequest($request);
        $reviews = $reviewRepository->findLimitOrder(4, 0);
        return $this->render('page/main_page.html.twig', [
            'reviews' => $reviews,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/home", name="app_home")
     */
    public function home()
    {
        return $this->render('page/home.html.twig', [
        ]);
    }

    /**
     * @Route("/articles", name="app_articles")
     */
    public function articles(
        ArticleRepository $articleRepository
    ) {
        $articles = $articleRepository->findAll();
        return $this->render('articles/index.html.twig', [
            'articles' => $articles
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
