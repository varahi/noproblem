<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Message;
use App\Entity\Article;
use App\Entity\City;
use App\Entity\ArticleCategory;
use App\Repository\ArticleRepository;
use App\Repository\ArticleCategoryRepository;
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

    private $security;

    /**
     * @param Environment $twig
     * @param ManagerRegistry $doctrine
     */
    public function __construct(
        Environment $twig,
        ManagerRegistry $doctrine,
        Security $security
    ) {
        $this->twig = $twig;
        $this->doctrine = $doctrine;
        $this->security = $security;
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
        $user = $this->security->getUser();

        $form = $this->createForm(MessageFormType::class, $message);
        $form->handleRequest($request);
        $reviews = $reviewRepository->findLimitOrder(4, 0);
        $categories = $categoryRepository->findAllOrder(['id' => 'ASC']);
        return $this->render('pages/main_page.html.twig', [
            'reviews' => $reviews,
            'categories' => $categories,
            'user' => $user,
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
     * @Route("/vacancies", name="app_vacancies")
     */
    public function vacanciesPage(
        Request $request,
        ReviewRepository $reviewRepository,
        CategoryRepository $categoryRepository
    ): Response {
        return new Response($this->twig->render('pages/sign-up.html.twig', [
        ]));
    }

    /**
     * @Route("/employee", name="app_employee")
     */
    public function employeePage(
        Request $request,
        ReviewRepository $reviewRepository,
        CategoryRepository $categoryRepository
    ): Response {
        return new Response($this->twig->render('pages/employee.html.twig', [
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
    public function blogList(
        ArticleRepository $articleRepository,
        ArticleCategoryRepository $articleCategoryRepository
    ) {
        $articles = $articleRepository->findLimitOrder('999', '0');
        $categories = $articleCategoryRepository->findAll();
        return $this->render('pages/blog/list.html.twig', [
            'articles' => $articles,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/blog/category/{slug}", name="app_blog_list_by_category")
     */
    public function blogListByCategory(
        ArticleRepository $articleRepository,
        ArticleCategoryRepository $articleCategoryRepository,
        ArticleCategory $articleCategory
    ) {
        $articles = $articleRepository->findByCategory($articleCategory->getId());
        $categories = $articleCategoryRepository->findAll();
        return $this->render('pages/blog/list_by_category.html.twig', [
            'articles' => $articles,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/blog/detail/{slug}", name="app_blog_detail")
     */
    public function blogDetail(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        Article $article,
        ArticleCategoryRepository $articleCategoryRepository
    ): Response {
        $categories = $articleCategoryRepository->findAll();
        return new Response($this->twig->render('pages/blog/detail.html.twig', [
            'article' => $article,
            'categories' => $categories
        ]));
    }

    /**
     * @Route("/courses", name="app_courses")
     */
    public function coursesList(
        ArticleRepository $articleRepository,
        ArticleCategoryRepository $articleCategoryRepository
    ) {
        //$articles = $articleRepository->findAll();
        //$categories = $articleCategoryRepository->findAll();
        return $this->render('pages/courses/list.html.twig', [
            //'articles' => $articles,
            //'categories' => $categories
        ]);
    }

    /**
     * @Route("/course/detail/{slug}", name="app_course_detail")
     */
    public function courseDetail(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        Course $course
    ): Response {
        return new Response($this->twig->render('pages/courses/detail.html.twig', [
            'course' => $course,
        ]));
    }
}
