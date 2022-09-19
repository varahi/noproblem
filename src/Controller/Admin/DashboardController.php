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
            ->setTitle('Noproblem Admin Panel');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('On site', 'fa fa-home', 'app_home');

        yield MenuItem::section('Database');
        yield MenuItem::subMenu('Database', 'fa fa-database')->setSubItems([
            MenuItem::linkToCrud('City', 'fa fa-building', City::class),
            MenuItem::linkToCrud('Category', 'fa fa-bars', Category::class),
            MenuItem::linkToCrud('Job', 'fa fa-handshake-o', Job::class),
        ]);

        /*yield MenuItem::section('Articles');
        yield MenuItem::subMenu('Articles', 'fa fa-tasks')->setSubItems([
            MenuItem::linkToCrud('Category', 'fa fa-folder-o', ArticleCategory::class),
            MenuItem::linkToCrud('Article', 'fa fa-folder-open-o', Article::class),
        ]);*/

        yield MenuItem::section('Site');
        yield MenuItem::subMenu('Site', 'fa fa-sitemap')->setSubItems([
            MenuItem::linkToCrud('Page', 'fa fa-paper-plane-o', Page::class),
            MenuItem::linkToCrud('Article Category', 'fa fa-folder-o', ArticleCategory::class),
            MenuItem::linkToCrud('Article', 'fa fa-folder-open-o', Article::class),
        ]);

        yield MenuItem::section('Users');
        yield MenuItem::linkToCrud('User', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Client', 'fa fa-user-circle', User::class)->setController(UserClientCrudController::class);

        yield MenuItem::section('Cources');
        yield MenuItem::linkToCrud('Course', 'fa fa-id-badge', Course::class);
    }
}
