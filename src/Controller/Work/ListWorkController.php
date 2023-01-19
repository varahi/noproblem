<?php

namespace App\Controller\Work;

use App\Controller\Traits\JobTrait;
use App\Repository\CategoryRepository;
use App\Repository\CitizenRepository;
use App\Repository\CityRepository;
use App\Repository\DistrictRepository;
use App\Repository\TaskRepository;
use App\Repository\WorksheetRepository;
use App\Service\CoordinateService;
use App\Service\ModalForms;
use App\Service\SessionService;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;
use Symfony\Component\Routing\Annotation\Route;

class ListWorkController extends AbstractController
{
    use JobTrait;

    public const LIMIT_PER_PAGE = '10';

    /**
     * @param Security $security
     * @param Environment $twig
     * @param ManagerRegistry $doctrine
     * @param ModalForms $modalForms
     * @param SessionService $sessionService
     */
    public function __construct(
        Security $security,
        Environment $twig,
        ManagerRegistry $doctrine,
        ModalForms $modalForms,
        SessionService $sessionService,
        CoordinateService $coordinateService
    ) {
        $this->security = $security;
        $this->twig = $twig;
        $this->doctrine = $doctrine;
        $this->modalForms = $modalForms;
        $this->sessionService = $sessionService;
        $this->coordinateService = $coordinateService;
    }


    /**
     * @Route("/workers", name="app_all_workers")
     */
    public function allWorkers(
        Request $request,
        CategoryRepository $categoryRepository,
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        WorksheetRepository $worksheetRepository,
        TaskRepository $taskRepository,
        PaginatorInterface $paginator,
        CitizenRepository $citizenRepository
    ): Response {
        $slug = $request->query->get('category');
        //$tags = $taskRepository->findAll();

        // Main params
        $user = $this->security->getUser() ?? null;
        $category = $categoryRepository->findOneBy(['slug' => $slug]) ?? null;

        // Request params
        $params = $request->query->all();
        $now = $params['now'] ?? '0';
        $age = $params['age'] ?? '';
        $payment = $params['payment'] ?? '';
        $busyness = $params['busyness'] ?? '';

        $cityId = trim($request->query->get('city'));
        $districtId = trim($request->query->get('district'));
        $district = $districtRepository->findOneBy(['id' => $districtId]);

        $cityRequest = $request->query->get('city');
        if (isset($cityRequest) && !empty($cityRequest)) {
            $city = $cityRepository->findOneBy(['id' => $cityId]);
            $cityName = $city->getName();
        } else {
            $cityName = $this->sessionService->getCity();
            $city = $cityRepository->findOneBy(['name' => $cityName]);
        }

        if (isset($params['citizen']) && $params['citizen'] !=='') {
            $citizen = $citizenRepository->findOneBy(['id' => $params['citizen']]);
        } else {
            $citizen = null;
        }

        if ($category !==null) {
            $tags = $taskRepository->findByCategory($category);
        } else {
            $tags = $taskRepository->findLimitOrder('999', '0');
        }

        if ($request->query->get('tag')) {
            $tasks = implode(',', $request->query->get('tag'));
        } else {
            $tasks = null;
        }

        $query = $worksheetRepository->findByParams($category, $tasks, $city, $citizen, $age, $now, $payment, $district, $busyness);
        $worksheets = $paginator->paginate($query, $request->query->getInt('page', 1), self::LIMIT_PER_PAGE);

        for ($i = 0; $i < 48; ++$i) {
            $ages[] = $i + 18;
        }

        return new Response($this->twig->render('pages/worksheet/all_workers.html.twig', [
            'cities' => $cityRepository->findLimitOrder('999', '0'),
            'city' => $city,
            'districts' => $districtRepository->findAll(),
            'worksheets' => $worksheets,
            'categories' => $categoryRepository->findLimitOrder('4', '0'),
            'category' => $category,
            'user' => $user,
            'cityId' => $cityId,
            'districtId' => $districtId,
            'featuredProfiles' => $this->getFeaturedProfiles($user),
            'slug' => $slug,
            'cityName' => $cityName,
            'tags' => $tags,
            'tasks' => $tasks,
            'ages' => $ages,
            'citizens' => $citizenRepository->findAll(),
            'hasCategory' => $request->query->has('category'),
            'lat' => $this->coordinateService->getLatArr($worksheets, $city),
            'lng' => $this->coordinateService->getLngArr($worksheets, $city),
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]));
    }
}
