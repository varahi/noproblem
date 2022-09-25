<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Entity\City;
use App\Entity\Category;
use App\Entity\Job;
use App\Entity\Article;
use App\Entity\ArticleCategory;
use App\Entity\Course;
use App\Entity\Page;
use App\Entity\Tariff;
use App\Entity\Review;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        //return parent::index();
        $routeBuilder = $this->get(AdminUrlGenerator::class);
        return $this->redirect($routeBuilder->setController(CityCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            //->setTitle('Noproblem')
            ->setTitle('<img src="assets/img/logo.svg" class="img-fluid d-block mx-auto" style="max-width:150px; width:100%;"><h2 class="mt-3 fw-bold text-black text-center" style="font-size: 22px;"></h2>')
            ->renderContentMaximized()
            ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('To Home', 'fa fa-home', 'app_main');

        yield MenuItem::section('Database');
        yield MenuItem::subMenu('Database', 'fa fa-database')->setSubItems([
            MenuItem::linkToCrud('Cities', 'fa fa-building', City::class),
            MenuItem::linkToCrud('Categories', 'fa fa-bars', Category::class),
            MenuItem::linkToCrud('Jobs', 'fa fa-handshake-o', Job::class),
        ]);
        yield MenuItem::section('Site');
        yield MenuItem::subMenu('Site', 'fa fa-sitemap')->setSubItems([
            MenuItem::linkToCrud('Pages', 'fa fa-paper-plane-o', Page::class),
            MenuItem::linkToCrud('Article Categories', 'fa fa-folder-o', ArticleCategory::class),
            MenuItem::linkToCrud('Articles', 'fa fa-folder-open-o', Article::class),
            MenuItem::linkToCrud('Reviews', 'fa fa-comment-o', Review::class),
        ]);

        yield MenuItem::section('Users');
        yield MenuItem::subMenu('Users', 'fa fa-users')->setSubItems([
            MenuItem::linkToCrud('User', 'fa fa-user', User::class),
            MenuItem::linkToCrud('Workers', 'fa fa-user-circle', User::class)->setController(UserEmployeeCrudController::class),
            MenuItem::linkToCrud('Customers', 'fa fa-user-circle-o', User::class)->setController(UserCustomerCrudController::class),
        ]);

        yield MenuItem::section('Cources');
        yield MenuItem::linkToCrud('Course', 'fa fa-id-badge', Course::class);

        yield MenuItem::section('Tariffs');
        yield MenuItem::linkToCrud('Tariff', 'fa fa-plane', Tariff::class);
    }
}
