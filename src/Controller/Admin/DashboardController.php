<?php

namespace App\Controller\Admin;

use App\Entity\Answer;
use App\Entity\Contain;
use App\Entity\District;
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
use App\Entity\Ticket;

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
            MenuItem::linkToCrud('Jobs', 'fa fa-handshake-o', Job::class),
            MenuItem::linkToCrud('Categories', 'fa fa-bars', Category::class),
            MenuItem::linkToCrud('Cities', 'fa fa-building', City::class),
            MenuItem::linkToCrud('District', 'fa fa-building', District::class),
        ]);

        yield MenuItem::section('Site');
        yield MenuItem::subMenu('Site', 'fa fa-sitemap')->setSubItems([
            MenuItem::linkToCrud('Pages', 'fa fa-paper-plane-o', Page::class),
            MenuItem::linkToCrud('Article Categories', 'fa fa-folder-o', ArticleCategory::class),
            MenuItem::linkToCrud('Articles', 'fa fa-folder-open-o', Article::class),
            MenuItem::linkToCrud('Reviews', 'fa fa-comment-o', Review::class),
        ]);

        yield MenuItem::section('Appeals');
        yield MenuItem::subMenu('Appeals', 'fa fa-reorder')->setSubItems([
            MenuItem::linkToCrud('Tickets', 'fa fa-ticket', Ticket::class),
            MenuItem::linkToCrud('Answers', 'fa fa fa-support', Answer::class),
        ]);

        yield MenuItem::section('Users');
        yield MenuItem::subMenu('Users', 'fa fa-users')->setSubItems([
            MenuItem::linkToCrud('Admins', 'fa fa-user', User::class)->setController(UserCrudController::class),
            MenuItem::linkToCrud('Workers', 'fa fa-user-circle', User::class)->setController(UserEmployeeCrudController::class),
            MenuItem::linkToCrud('Customers', 'fa fa-user-circle-o', User::class)->setController(UserCustomerCrudController::class),
        ]);

        yield MenuItem::section('Cources');
        yield MenuItem::subMenu('Cources', 'fa fa-id-badge')->setSubItems([
            MenuItem::linkToCrud('Course', 'fa fa-cube', Course::class),
            MenuItem::linkToCrud('Contain', 'fa fa-cubes', Contain::class),
        ]);

        yield MenuItem::section('Tariffs');
        yield MenuItem::linkToCrud('Tariff', 'fa fa-volume-up', Tariff::class);
    }
}
