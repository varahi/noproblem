<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Article;
use App\Entity\City;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
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
use Twig\Environment;
use Doctrine\Persistence\ManagerRegistry;

class PageController extends AbstractController
{
    private $twig;

    /**
     * @param Environment $twig
     * @param ManagerRegistry $doctrine
     */
    public function __construct(
        Environment $twig,
        ManagerRegistry $doctrine
    ) {
        $this->twig = $twig;
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/", name="app_main")
     */
    public function mainPage(
        Request $request,
        ReviewRepository $reviewRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $message = new Message();
        $form = $this->createForm(MessageFormType::class, $message);
        $form->handleRequest($request);
        $reviews = $reviewRepository->findLimitOrder(4, 0);
        $categories = $categoryRepository->findAllOrder(['id' => 'ASC']);
        return $this->render('pages/main_page.html.twig', [
            'reviews' => $reviews,
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/company", name="app_company")
     */
    public function companyPage(
        Request $request,
        ReviewRepository $reviewRepository,
        CategoryRepository $categoryRepository
    ): Response {
        return new Response($this->twig->render('pages/company.html.twig', [
        ]));
    }

    /**
     * @Route("/tarifs", name="app_tarifs")
     */
    public function tarifsPage(
        Request $request,
        ReviewRepository $reviewRepository,
        CategoryRepository $categoryRepository
    ): Response {
        return new Response($this->twig->render('pages/tarifs.html.twig', [
        ]));
    }

    /**
     * @Route("/home", name="app_home")
     */
    public function home()
    {
        return $this->render('pages/home.html.twig', [
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
     * @Route("/blog", name="app_blog")
     */
    public function blogPage(
        ArticleRepository $articleRepository
    ) {
        $articles = $articleRepository->findAll();
        return $this->render('pages/blog/list.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/blog/detail/{slug}", name="app_blog_detail")
     */
    public function blogDetailPage(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        Article $article
    ): Response {
        return new Response($this->twig->render('pages/blog/detail.html.twig', [
            'article' => $article
        ]));
    }
}
