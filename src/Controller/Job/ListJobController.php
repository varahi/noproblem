<?php

namespace App\Controller\Job;

use App\Controller\Traits\AbstractTrait;
use App\Controller\Traits\DataTrait;
use App\Controller\Traits\JobTrait;
use App\Entity\AdditionalInfo;
use App\Repository\AdditionalInfoRepository;
use App\Repository\CategoryRepository;
use App\Repository\CitizenRepository;
use App\Repository\CityRepository;
use App\Repository\DistrictRepository;
use App\Repository\EducationRepository;
use App\Repository\ExperienceRepository;
use App\Repository\JobRepository;
use App\Repository\TaskRepository;
use App\Service\CoordinateService;
use App\Service\ModalForms;
use App\Service\SessionService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;
use Knp\Component\Pager\PaginatorInterface;
use App\ImageOptimizer;

class ListJobController extends AbstractController
{
    use JobTrait;

    use DataTrait;

    use AbstractTrait;

    public const LIMIT_PER_PAGE = '10';

    public function __construct(
        Security $security,
        Environment $twig,
        ManagerRegistry $doctrine,
        ModalForms $modalForms,
        ImageOptimizer $imageOptimizer,
        string $targetDirectory,
        SessionService $sessionService,
        CoordinateService $coordinateService
    ) {
        $this->security = $security;
        $this->twig = $twig;
        $this->doctrine = $doctrine;
        $this->modalForms = $modalForms;
        $this->imageOptimizer = $imageOptimizer;
        $this->targetDirectory = $targetDirectory;
        $this->sessionService = $sessionService;
        $this->coordinateService = $coordinateService;
    }

    /**
     *
     * @Route("/vacancies", name="app_all_jobs")
     */
    public function allJobs(
        Request $request,
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        CategoryRepository $categoryRepository,
        JobRepository $jobRepository,
        TaskRepository $taskRepository,
        PaginatorInterface $paginator,
        CitizenRepository $citizenRepository,
        EducationRepository $educationRepository,
        AdditionalInfoRepository $additionalInfoRepository,
        ExperienceRepository $experienceRepository
    ): Response {
        $slug = $request->query->get('category');
        $districts = $districtRepository->findAll();

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

        $queryJobs = $jobRepository->findByParams($category, $tasks, $city, $citizen, $age, $now, $payment, $district, $busyness);
        $jobs = $paginator->paginate(
            $queryJobs,
            $request->query->getInt('page', 1),
            self::LIMIT_PER_PAGE
        );

        // Resize avatar if exist
        if ($jobs) {
            foreach ($jobs as $job) {
                if ($job->getOwner() && $job->getOwner()->getAvatar()) {
                    $this->imageOptimizer->resize($this->targetDirectory.'/'.$job->getOwner()->getAvatar());
                }
            }
        }

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
            '5' => 'другое'
        ];

        $childrenQtys = [
            '0' => '1 ребенок',
            '1' => '2 ребенка',
            '2' => '3 ребенка',
            '3' => '4 ребенка',
            '4' => 'другое',
        ];

        $patientAges = [
            '0' => 'Дети до 10 лет',
            '1' => 'Подростки с 11 до 18 лет',
            '2' => 'Взрослые с 19 до 59 лет',
            '3' => 'Пожилые от 60 и старше',
            '4' => 'другое'
        ];

        $schedules = [
            '0' => '1 раз в неделю',
            '1' => 'Несколько раз в неделю',
            '2' => '1 раз в месяц',
            '3' => 'С проживанием',
        ];

        $tools = [
            '0' => 'Работа моими инструментами',
            '1' => 'Работа с нашими инструментами',
            '2' => 'По договорённости'
        ];

        $cleanings = [
            '0' => 'Своими средствами',
            '1' => 'Нашими средствами',
            '2' => 'По договорённости',
        ];

        return new Response($this->twig->render('pages/job/all_jobs.html.twig', [
            'city' => $city,
            'districts' => $districts,
            'jobs' => $jobs,
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
            'categories' => $categoryRepository->findLimitOrder('4', '0'),
            'featuredJobs' => $this->getFeaturedJobs($user),
            'citizens' => $citizenRepository->findAll(),
            'hasCategory' => $request->query->has('category'),
            'lat' => $this->coordinateService->getLatArr($jobs, $city),
            'lng' => $this->coordinateService->getLngArr($jobs, $city),
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]));
    }
}
