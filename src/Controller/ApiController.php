<?php

namespace App\Controller;

use App\Controller\Traits\DataTrait;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\ArticleCategoryRepository;
use App\Repository\CourseRepository;
use App\Repository\JobRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ApiController extends AbstractController
{
    use DataTrait;

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
        if(isset($user) && $user !==null) {
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
        $items = $categoryRepository->findAllOrder(['id' => 'ASC']);
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
}
