<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\District;
use App\Entity\Message;
use App\Entity\Article;
use App\Entity\City;
use App\Entity\ArticleCategory;
use App\Entity\Worksheet;
use App\Repository\ArticleRepository;
use App\Repository\ArticleCategoryRepository;
use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use App\Repository\DistrictRepository;
use App\Repository\PageRepository;
use App\Repository\ReviewRepository;
use App\Repository\TariffRepository;
use App\Repository\WorksheetRepository;
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
use App\Service\ModalForms;

class PageController extends AbstractController
{
    private $twig;

    private $security;


    /**
     * @param Environment $twig
     * @param ManagerRegistry $doctrine
     * @param Security $security
     * @param ModalForms $modalForms
     */
    public function __construct(
        Environment $twig,
        ManagerRegistry $doctrine,
        Security $security,
        ModalForms $modalForms
    ) {
        $this->twig = $twig;
        $this->doctrine = $doctrine;
        $this->security = $security;
        $this->modalForms = $modalForms;
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
            'form' => $form->createView(),
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
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
        $user = $this->security->getUser();
        return new Response($this->twig->render('pages/company.html.twig', [
            'user' => $user,
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]));
    }

    /**
     * @Route("/tarifs", name="app_tarifs")
     */
    public function tarifsPage(
        Request $request,
        ReviewRepository $reviewRepository,
        CategoryRepository $categoryRepository,
        TariffRepository $tariffRepository
    ): Response {
        $user = $this->security->getUser();
        $tariffs = $tariffRepository->findAll();
        return new Response($this->twig->render('pages/tarifs.html.twig', [
            'user' => $user,
            'tariffs' => $tariffs,
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]));
    }

    /**
     * @Route("/blog", name="app_blog")
     */
    public function blogList(
        Request $request,
        ArticleRepository $articleRepository,
        ArticleCategoryRepository $articleCategoryRepository
    ) {
        $articles = $articleRepository->findLimitOrder('999', '0');
        $categories = $articleCategoryRepository->findAll();
        $user = $this->security->getUser();
        return $this->render('pages/blog/list.html.twig', [
            'articles' => $articles,
            'categories' => $categories,
            'user' => $user,
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]);
    }

    /**
     * @Route("/blog/category/{slug}", name="app_blog_list_by_category")
     */
    public function blogListByCategory(
        Request $request,
        ArticleRepository $articleRepository,
        ArticleCategoryRepository $articleCategoryRepository,
        ArticleCategory $articleCategory
    ) {
        $articles = $articleRepository->findByCategory($articleCategory->getId());
        $categories = $articleCategoryRepository->findAll();
        $user = $this->security->getUser();

        return $this->render('pages/blog/list_by_category.html.twig', [
            'articles' => $articles,
            'categories' => $categories,
            'user' => $user,
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
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
        $user = $this->security->getUser();
        return new Response($this->twig->render('pages/blog/detail.html.twig', [
            'article' => $article,
            'categories' => $categories,
            'user' => $user,
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]));
    }

    /**
     * @Route("/courses", name="app_courses")
     */
    public function coursesList(
        Request $request,
        ArticleRepository $articleRepository,
        ArticleCategoryRepository $articleCategoryRepository
    ) {
        //$articles = $articleRepository->findAll();
        //$categories = $articleCategoryRepository->findAll();
        $user = $this->security->getUser();
        return $this->render('pages/courses/list.html.twig', [
            'ticketForm' => $this->modalForms->ticketForm($request)->createView(),
            'user' => $user
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
        $user = $this->security->getUser();
        return new Response($this->twig->render('pages/courses/detail.html.twig', [
            'course' => $course,
            'user' => $user,
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]));
    }

    /**
     * @Route("/politica", name="app_page_politica")
     */
    public function privacyPage(
        Request $request,
        PageRepository $pageRepository
    ): Response {
        //$user = $this->security->getUser();
        $content = $pageRepository->findById(2);
        $user = $this->security->getUser();
        return $this->render('pages/privacy.html.twig', [
            'content' => $content,
            'user' => $user,
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]);
    }

    /**
     * @Route("/personal-data", name="app_page_personal_data")
     */
    public function personalDataPage(
        Request $request,
        PageRepository $pageRepository
    ): Response {
        $content = $pageRepository->findById(1);
        $user = $this->security->getUser();
        return $this->render('pages/personal.html.twig', [
            'content' => $content,
            'user' => $user,
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]);
    }
}
