<?php

namespace App\Controller\Work;

use App\Controller\Traits\JobTrait;
use App\Repository\AdditionalInfoRepository;
use App\Repository\CategoryRepository;
use App\Repository\CitizenRepository;
use App\Repository\CityRepository;
use App\Repository\DistrictRepository;
use App\Repository\EducationRepository;
use App\Repository\ExperienceRepository;
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
        CitizenRepository $citizenRepository,
        EducationRepository $educationRepository,
        AdditionalInfoRepository $additionalInfoRepository,
        ExperienceRepository $experienceRepository
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
        $educationId = $params['education'] ?? '';
        $accommodation = $params['accommodation'] ?? '';
        $cityId = $params['city'] ?? '';
        $districtId = $params['district'] ?? '';

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

        for ($i = 0; $i < 500; ++$i) {
            $squares[] = $i + 1;
        }

        $childrenAges = [
            '0' => '0-1 год',
            '1' => '2-3 года',
            '2' => '4-6 лет',
            '3' => '7-10 лет',
            '4' => '2-3 года',
            '5' => 'Другое'
        ];

        $childrenQtys = [
            '0' => '1 ребёнок',
            '1' => '2 ребёнка',
            '2' => '3 ребёнка',
            '3' => '4 ребёнка',
            '4' => 'другое',
        ];

        $patientAges = [
            '0' => 'Дети до 10 лет',
            '1' => 'Подростки с 11 до 18 лет',
            '2' => 'Взрослые с 19 до 59 лет',
            '3' => 'Пожилые от 60 и старше',
            '4' => 'Другое'
        ];

        $schedules = [
            '0' => '1 раз в неделю',
            '1' => 'Несколько раз в неделю',
            '2' => '1 раз в месяц',
        ];

        $tools = [
            '0' => 'Работа со своими инструментами',
            '1' => 'Работа с моими инструментами',
            '2' => 'По договорённости'
        ];

        $cleanings = [
            '0' => 'Своими средствами',
            '1' => 'Моими средствами',
            '2' => 'По договорённости',
        ];

        return new Response($this->twig->render('pages/worksheet/all_workers.html.twig', [
            'city' => $city,
            'worksheets' => $worksheets,
            'category' => $category,
            'user' => $user,
            'cityId' => $cityId,
            'districtId' => $districtId,
            'slug' => $slug,
            'cityName' => $cityName,
            'tags' => $tags,
            'tasks' => $tasks,
            'ages' => $ages,
            'squares' => $squares,
            'educationId' => $educationId,
            'childrenAges' => $childrenAges,
            'childrenQtys' => $childrenQtys,
            'patientAges' => $patientAges,
            'schedules' => $schedules,
            'tools' => $tools,
            'cleanings' => $cleanings,
            'experiences' => $experienceRepository->findAll(),
            'accommodation' => $params['accommodation'] ?? '',
            'additionalInfo' => $params['additionalInfo'] ?? '',
            'additionalInfos' => $additionalInfoRepository->findAll(),
            'educations' => $educationRepository->findAll(),
            'cities' => $cityRepository->findLimitOrder('999', '0'),
            'districts' => $districtRepository->findAll(),
            'categories' => $categoryRepository->findLimitOrder('4', '0'),
            'featuredProfiles' => $this->getFeaturedProfiles($user),
            'citizens' => $citizenRepository->findAll(),
            'hasCategory' => $request->query->has('category'),
            'lat' => $this->coordinateService->getLatArr($worksheets, $city),
            'lng' => $this->coordinateService->getLngArr($worksheets, $city),
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]));
    }
}
