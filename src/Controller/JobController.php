<?php

namespace App\Controller;

use App\Controller\Traits\AbstractTrait;
use App\Controller\Traits\DataTrait;
use App\Controller\Traits\JobTrait;
use App\Controller\Traits\MapTrait;
use App\Entity\City;
use App\Entity\Job;
use App\Entity\Worksheet;
use App\Form\Job\JobFormType;
use App\Form\Worksheet\WorksheetFormType;
use App\Repository\AccommodationRepository;
use App\Repository\AdditionalInfoRepository;
use App\Repository\BusynessRepository;
use App\Repository\CategoryRepository;
use App\Repository\CitizenRepository;
use App\Repository\CityRepository;
use App\Repository\DistrictRepository;
use App\Repository\JobRepository;
use App\Repository\TaskRepository;
use App\Repository\WorksheetRepository;
use App\Service\FileUploader;
use App\Service\ModalForms;
use App\Service\SessionService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Twig\Environment;
use Knp\Component\Pager\PaginatorInterface;
use App\ImageOptimizer;

class JobController extends AbstractController
{
    use JobTrait;

    use DataTrait;

    use AbstractTrait;

    use MapTrait;

    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';

    public const ROLE_BUYER = 'ROLE_BUYER';

    private $security;

    public const LIMIT_PER_PAGE = '10';

    public function __construct(
        Security $security,
        Environment $twig,
        ManagerRegistry $doctrine,
        ModalForms $modalForms,
        ImageOptimizer $imageOptimizer,
        string $targetDirectory,
        SessionService $sessionService
    ) {
        $this->security = $security;
        $this->twig = $twig;
        $this->doctrine = $doctrine;
        $this->modalForms = $modalForms;
        $this->imageOptimizer = $imageOptimizer;
        $this->targetDirectory = $targetDirectory;
        $this->sessionService = $sessionService;
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
        PaginatorInterface $paginator
    ): Response {
        $slug = $request->query->get('category');
        $cities = $cityRepository->findLimitOrder('999', '0');
        $districts = $districtRepository->findAll();
        $categories = $categoryRepository->findAll();

        // Get different get params
        $cityId = trim($request->query->get('city'));
        $districtId = trim($request->query->get('district'));

        // If city in session not null // else get city from POST
        $cityRequest = $request->query->get('city');
        if (isset($cityRequest) && !empty($cityRequest)) {
            $city = $cityRepository->findOneBy(['id' => $cityId]);
            $cityName = $city->getName();
        } else {
            $cityName = $this->sessionService->getCity();
            $city = $cityRepository->findOneBy(['name' => $cityName]);
        }

        $district = $districtRepository->findOneBy(['id' => $districtId]);

        if ($this->security->getUser()) {
            $user = $this->security->getUser();
        } else {
            $user = null;
        }

        if ($slug !== '') {
            $category = $categoryRepository->findOneBy(['slug' => $slug]);
        } else {
            $category = null;
        }

        $queryJobs = $jobRepository->findByParams($category, $city, $district);
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

        $featuredJobs = $this->getFeaturedJobs($user);
        return new Response($this->twig->render('pages/job/all_jobs.html.twig', [
            'cities' => $cities,
            'city' => $city,
            'districts' => $districts,
            'jobs' => $jobs,
            'categories' => $categories,
            'category' => $category,
            'user' => $user,
            'cityId' => $cityId,
            'districtId' => $districtId,
            'featuredJobs' => $featuredJobs,
            'slug' => $slug,
            'cityName' => $cityName,
            'lat' => $this->getLatArr($jobs),
            'lng' => $this->getLngArr($jobs),
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]));
    }


    /**
     *
     * @IsGranted("ROLE_EMPLOYEE")
     * @Route("/new-job", name="app_new_job")
     */
    public function newJob(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        ManagerRegistry $doctrine,
        TaskRepository $taskRepository,
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        CitizenRepository $citizenRepository,
        AdditionalInfoRepository $additionalInfoRepository,
        AccommodationRepository $accommodationRepository,
        BusynessRepository $busynessRepository,
        FileUploader $fileUploader
    ): Response {
        if ($this->isGranted(self::ROLE_EMPLOYEE)) {
            $user = $this->security->getUser();
            $tasks = $taskRepository->findAllOrder(['id' => 'ASC']);
            $citizens = $citizenRepository->findAllOrder(['id' => 'ASC']);
            $cities = $cityRepository->findLimitOrder('999', '0');
            $districts = $districtRepository->findLimitOrder('999', '0');
            $additionals = $additionalInfoRepository->findAll();
            $accommodations = $accommodationRepository->findAll();
            $busynessess = $busynessRepository->findAll();

            $job = new Job();
            $url = $this->generateUrl('app_new_job');
            $form = $this->createForm(JobFormType::class, $job, [
                'action' => $url,
                'method' => 'POST',
            ]);

            // Days array for scheduler
            $daysArr = [
                0 => 'Пн',
                1 => 'Вт',
                2 => 'Ср',
                3 => 'Чт',
            ];
            $daysArr2 = [
                4 => 'Пт',
                5 => 'Сб',
                6 => 'Вс',
            ];
            foreach ($daysArr as $key => $day) {
                $days1[] = [
                    "week" => $day,
                    "startTime" => "12:00",
                    "endTime" => "16:00",
                    'checked' => 1,
                    'key' => $key
                ];
            }
            foreach ($daysArr2 as $key => $day) {
                $days2[] = [
                    "week" => $day,
                    "startTime" => "12:00",
                    "endTime" => "16:00",
                    'checked' => 1,
                    'key' => $key
                ];
            }

            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                $post = $request->request->get('job_form');
                $city = $cityRepository->findOneBy(['name' => $post['city']]);

                if ($city == null) {
                    $city = $this->setNewCity($request, 'job_form');
                }
                $job->setCity($city);

                // Set coordinates by address
                $this->setCoordsByAddress($cityRepository, $job);

                // Update fields
                $this->updateFields(
                    $request,
                    $form,
                    $job,
                    $cityRepository,
                    $districtRepository,
                    $taskRepository,
                    $citizenRepository,
                    $additionalInfoRepository,
                    $accommodationRepository,
                    $busynessRepository,
                    $fileUploader
                );

                $message = $translator->trans('New job created', array(), 'flash');
                $notifier->send(new Notification($message, ['browser']));
                $referer = $request->headers->get('referer');
                return new RedirectResponse($referer);
            }

            return $this->render('pages/job/new.html.twig', [
                'user' => $user,
                'tasks' => $tasks,
                'cities' => $cities,
                'districts' => $districts,
                'citizens' => $citizens,
                'additionals' => $additionals,
                'accommodations' => $accommodations,
                'busynessess' => $busynessess,
                'days1' => $days1,
                'days2' => $days2,
                'form' => $form->createView(),
                'cityName' => $this->sessionService->getCity(),
                'ticketForm' => $this->modalForms->ticketForm($request)->createView()
            ]);
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }
    }

    /**
     *
     * @IsGranted("ROLE_EMPLOYEE")
     * @Route("/edit-job/job-{id}", name="app_edit_job")
     */
    public function editJob(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        ManagerRegistry $doctrine,
        TaskRepository $taskRepository,
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        CitizenRepository $citizenRepository,
        AdditionalInfoRepository $additionalInfoRepository,
        AccommodationRepository $accommodationRepository,
        BusynessRepository $busynessRepository,
        FileUploader $fileUploader,
        Job $job
    ): Response {
        if ($this->isGranted(self::ROLE_EMPLOYEE)) {
            $user = $this->security->getUser();
            // Redirect if not owner
            if ($user->getId() !== $job->getOwner()->getId()) {
                $message = $translator->trans('Please login', array(), 'flash');
                $notifier->send(new Notification($message, ['browser']));
                return $this->redirectToRoute("app_main");
            }

            $tasks = $taskRepository->findAllOrder(['id' => 'ASC']);
            $citizens = $citizenRepository->findAllOrder(['id' => 'ASC']);
            $cities = $cityRepository->findLimitOrder('999', '0');
            $districts = $districtRepository->findLimitOrder('999', '0');
            $additionals = $additionalInfoRepository->findAll();
            $accommodations = $accommodationRepository->findAll();
            $busynessess = $busynessRepository->findAll();

            $url = $this->generateUrl('app_edit_job', ['id' => $job->getId()]);
            $form = $this->createForm(JobFormType::class, $job, [
                'action' => $url,
                'method' => 'POST',
            ]);

            // Get selected checkboxes
            if (count($job->getTasks()) > 0) {
                foreach ($job->getTasks() as $task) {
                    $selectedTasksArr[] = $task->getId();
                }
            } else {
                $selectedTasksArr = null;
            }

            if (count($job->getAccommodations()) > 0) {
                foreach ($job->getAccommodations() as $accommodation) {
                    $accommodationArr[] = $accommodation->getId();
                }
            } else {
                $accommodationArr = null;
            }

            if (count($job->getBusynesses()) > 0) {
                foreach ($job->getBusynesses() as $busyness) {
                    $busynessArr[] = $busyness->getId();
                }
            } else {
                $busynessArr = null;
            }

            if (count($job->getAdditional()) > 0) {
                foreach ($job->getAdditional() as $additional) {
                    $additionalArr[] = $additional->getId();
                }
            } else {
                $additionalArr = null;
            }

            // Days array for scheduler
            $daysArr = [
                0 => 'Пн',
                1 => 'Вт',
                2 => 'Ср',
                3 => 'Чт',
            ];
            $daysArr2 = [
                4 => 'Пт',
                5 => 'Сб',
                6 => 'Вс',
            ];
            foreach ($daysArr as $key => $day) {
                $days1[] = [
                    "week" => $day,
                    "startTime" => "12:00",
                    "endTime" => "16:00",
                    'checked' => 1,
                    'key' => $key
                ];
            }
            foreach ($daysArr2 as $key => $day) {
                $days2[] = [
                    "week" => $day,
                    "startTime" => "12:00",
                    "endTime" => "16:00",
                    'checked' => 1,
                    'key' => $key
                ];
            }

            $form->handleRequest($request);
            if ($form->isSubmitted()) {

                // Set coordinates by address
                $this->setCoordsByAddress($cityRepository, $job);

                // Update fields
                $this->updateFields(
                    $request,
                    $form,
                    $job,
                    $cityRepository,
                    $districtRepository,
                    $taskRepository,
                    $citizenRepository,
                    $additionalInfoRepository,
                    $accommodationRepository,
                    $busynessRepository,
                    $fileUploader
                );

                $message = $translator->trans('Job updated', array(), 'flash');
                $notifier->send(new Notification($message, ['browser']));
                $referer = $request->headers->get('referer');
                return new RedirectResponse($referer);
            }

            return $this->render('pages/job/edit.html.twig', [
                'user' => $user,
                'job' => $job,
                'tasks' => $tasks,
                'cities' => $cities,
                'districts' => $districts,
                'citizens' => $citizens,
                'additionals' => $additionals,
                'accommodations' => $accommodations,
                'busynessess' => $busynessess,
                'days1' => $days1,
                'days2' => $days2,
                'selectedTasksArr' => $selectedTasksArr,
                'accommodationArr' => $accommodationArr,
                'busynessArr' => $busynessArr,
                'additionalArr' => $additionalArr,
                'form' => $form->createView(),
                'cityName' => $this->sessionService->getCity(),
                'ticketForm' => $this->modalForms->ticketForm($request)->createView()
            ]);
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }
    }

    private function setCoordsByAddress(
        CityRepository $cityRepository,
        Job $job
    ) {
        // Set coordinates by address
        if (isset($_POST['job_form']['address']) && $_POST['job_form']['address'] !=='') {
            $post = $_POST['job_form'];
            $address = $post['address'];
            $address = str_replace(" ", "+", $address);
            $city = $cityRepository->findOneBy(['name' => $post['city']]);

            if ($city instanceof City) {
                // Serach by address
                $urlRequest = 'https://nominatim.openstreetmap.org/search.php?q='.$address.'+'.$city->getName().'&countrycodes=ru&limit=1&format=jsonv2';

                // Create a stream
                $opts = array('http'=>array('header'=>"User-Agent: StevesCleverAddressScript 3.7.6\r\n"));
                $context = stream_context_create($opts);

                // Open the file using the HTTP headers set above
                $data = file_get_contents($urlRequest, false, $context);
                $json = json_decode($data);
                foreach ($json as $data) {
                    $lat = $data->lat;
                    $lon = $data->lon;
                }

                if (isset($lat) && isset($lon)) {
                    $job->setLatitude($lat);
                    $job->setLongitude($lon);
                } else {
                    $job->setLatitude($city->getLatitude());
                    $job->setLongitude($city->getLongitude());
                }

                $entityManager = $this->doctrine->getManager();
                $entityManager->persist($job);
                $entityManager->flush();
            }
        }
    }

    /**
     * @param Request $request
     * @param $form
     * @param Job $job
     * @param CityRepository $cityRepository
     * @param DistrictRepository $districtRepository
     * @param TaskRepository $taskRepository
     * @param CitizenRepository $citizenRepository
     * @param AdditionalInfoRepository $additionalInfoRepository
     * @param AccommodationRepository $accommodationRepository
     * @param BusynessRepository $busynessRepository
     * @param FileUploader $fileUploader
     * @return void
     */
    private function updateFields(
        Request $request,
        $form,
        Job $job,
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        TaskRepository $taskRepository,
        CitizenRepository $citizenRepository,
        AdditionalInfoRepository $additionalInfoRepository,
        AccommodationRepository $accommodationRepository,
        BusynessRepository $busynessRepository,
        FileUploader $fileUploader
    ) {
        $entityManager = $this->doctrine->getManager();
        $user = $this->security->getUser();
        $post = $request->request->get('job_form');

        // Week days start time and end time
        if (!empty($post['week']) && is_array($post['week'])) {
            foreach ($post['week'] as $key => $week) {
                /*if ($week['startTime'] !=='' && $week['endTime'] !=='' && isset($week['weekDay'])) {
                    $weekArr[] = $week;
                }*/
                $weekArr[] = $week;
            }
        }

        // Generate json to put in database
        if (isset($weekArr) && is_array($weekArr)) {
            $scheduleArrJson = json_encode($weekArr);
        } else {
            $scheduleArrJson = null;
        }

        $job->setOwner($user);
        $job->setSchedule($scheduleArrJson);

        /*if (isset($post['city'])&& $post['city'] !=='') {
            $city = $cityRepository->findOneBy(['id' => $post['city']]);
            $job->setCity($city);
        }*/

        $city = $cityRepository->findOneBy(['name' => $post['city']]);
        if ($city == null) {
            $city = $this->setNewCity($request, 'job_form');
        }
        $job->setCity($city);

        if (isset($post['district']) && $post['district'] !=='') {
            $district = $districtRepository->findOneBy(['id' => $post['district']]);
            $job->setDistrict($district);
        }
        if (isset($post['task']) && $post['task'] !=='' && is_array($post['task'])) {
            foreach ($post['task'] as $taskId) {
                $task = $taskRepository->findOneBy(['id' => $taskId]);
                $job->addTask($task);
                $entityManager->persist($task);
            }
        }
        if (isset($post['citizenship']) && $post['citizenship'] !=='') {
            $citizen = $citizenRepository->findOneBy(['id' => $post['citizenship']]);
            $job->setCitizen($citizen);
        }
        if (isset($post['accommodation']) && $post['accommodation'] !=='') {
            $accommodation = $accommodationRepository->findOneBy(['id' => $post['accommodation']]);
            $job->addAccommodation($accommodation);
        }
        if (isset($post['busyness']) && $post['busyness'] !=='') {
            $busyness = $busynessRepository->findOneBy(['id' => $post['busyness']]);
            $job->addBusyness($busyness);
        }
        if (isset($post['additionally']) && $post['additionally'] && is_array($post['additionally'])) {
            foreach ($post['additionally'] as $additionalId) {
                $additionally = $additionalInfoRepository->findOneBy(['id' => $additionalId]);
                $job->addAdditional($additionally);
                $entityManager->persist($additionally);
            }
        }

        // File upload
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $imageFileName = $fileUploader->upload($imageFile);
            $job->setImage($imageFileName);
        }

        $entityManager->persist($job);
        $entityManager->flush();
    }

    /**
     *
     * @Route("/detail-job/job-{id}", name="app_detail_job")
     */
    public function detailJob(
        Request $request,
        Job $job,
        JobRepository $jobRepository
    ): Response {
        $category = $job->getCategory();
        $relatedJobs = $jobRepository->findByCategory($category->getId(), $job->getId(), '10');
        $user = $this->security->getUser();

        // Times array
        for ($i = 0; $i < 12; ++$i) {
            $timesArray[] = $i + 10;
        }
        // Dates array
        $daysArray = [
            0 => 'Пн',
            1 => 'Вт',
            2 => 'Ср',
            3 => 'Чт',
            4 => 'Пт',
            5 => 'Сб',
            6 => 'Вс',
        ];

        $schedules = json_decode($job->getSchedule(), true);
        if (is_array($schedules) && !empty($schedules)) {
            foreach ($schedules as $key => $schedule) {
                $scheduleKeys[] = $key;
                if (array_key_exists('weekDay', $schedule)) {
                    $dayKeys[$key]['day'] = $schedule['weekDay'];
                    $dayKeys[$key]['startTime'] = $schedule['startTime'];
                    $dayKeys[$key]['endTime'] = $schedule['endTime'];
                    //$dayKeys[] = $schedule['weekDay'];
                }
            }
        } else {
            $dayKeys = null;
            $scheduleKeys = null;
        }

        for ($i = 0; $i < 4; ++$i) {
            $testArray[] = $i + 11;
        }

        //dd($dayKeys);

        $featuredJobs = $this->getFeaturedJobs($user);

        return new Response($this->twig->render('pages/job/detail.html.twig', [
            'user' => $user,
            'job' => $job,
            'timesArray' => $timesArray,
            'daysArray' => $daysArray,
            'relatedJobs' => $relatedJobs,
            'schedules' => $schedules,
            'dayKeys' => $dayKeys,
            'scheduleKeys' => $scheduleKeys,
            'featuredJobs' => $featuredJobs,
            'testArray' => $testArray,
            'cityName' => $this->sessionService->getCity(),
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]));
    }

    /**
     *
     * @IsGranted("ROLE_EMPLOYEE")
     * @Route("/profile/my-jobs", name="app_my_jobs")
     */
    public function myJobs(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        ManagerRegistry $doctrine,
        JobRepository $jobRepository
    ): Response {
        if ($this->isGranted(self::ROLE_EMPLOYEE)) {
            $user = $this->security->getUser();
            $jobs = $jobRepository->findByUser($user->getId());
            {
                return $this->render('pages/job/my_jobs.html.twig', [
                    'user' => $user,
                    'jobs' => $jobs,
                    'cityName' => $this->sessionService->getCity(),
                    'ticketForm' => $this->modalForms->ticketForm($request)->createView()
                ]);
            }
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }
    }

    /**
     *
     * @Route("/selected-jobs", name="app_selected_jobs")
     */
    public function selectedJobs(
        Request $request,
        JobRepository $jobRepository,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        PaginatorInterface $paginator
    ): Response {
        if ($this->isGranted(self::ROLE_EMPLOYEE) || $this->isGranted(self::ROLE_CUSTOMER)) {
            $user = $this->security->getUser();
            $query = $jobRepository->findSelectedProfiles($user);

            $jobs = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                self::LIMIT_PER_PAGE
            );

            $user = $this->security->getUser();
            $featuredJobs = $this->getFeaturedJobs($user);

            return new Response($this->twig->render('user/selected_jobs.html.twig', [
                'user' => $user,
                'jobs' => $jobs,
                'featuredJobs' => $featuredJobs,
                'cityName' => $this->sessionService->getCity(),
                'ticketForm' => $this->modalForms->ticketForm($request)->createView()
            ]));
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }
    }
}
