<?php

namespace App\Controller;

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

class JobController extends AbstractController
{
    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';

    public const ROLE_BUYER = 'ROLE_BUYER';

    private $security;

    public const LIMIT_PER_PAGE = '5';

    public function __construct(
        Security $security,
        Environment $twig,
        ManagerRegistry $doctrine
    ) {
        $this->security = $security;
        $this->twig = $twig;
        $this->doctrine = $doctrine;
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
        $cities = $cityRepository->findAll();
        $districts = $districtRepository->findAll();
        $categories = $categoryRepository->findAll();

        $cityId = trim($request->query->get('city'));
        $districtId = trim($request->query->get('district'));
        $city = $cityRepository->findOneBy(['id' => $cityId]);
        $district = $districtRepository->findOneBy(['id' => $districtId]);

        if ($this->security->getUser()) {
            $user = $this->security->getUser();
        } else {
            $user = null;
        }

        if ($slug == '') {
            $queryJobs = $jobRepository->findAll();
            $category = null;
            if ($cityId !== '' && $districtId !== '' || $cityId !== '') {
                $queryJobs = $jobRepository->findByParams($city, $district);
            }
        } else {
            $category = $categoryRepository->findOneBy(['slug' => $slug]);
            $queryJobs = $jobRepository->findBy(['category' => $category]);

            /*if($cityId !== '' && $districtId !== '' || $cityId !== '' ) {
                $jobs = $jobRepository->findByParams($city, $district, $slug);
            } else {
                $jobs = $jobRepository->findBy(['category' => $category]);
            }*/
        }

        $jobs = $paginator->paginate(
            $queryJobs,
            $request->query->getInt('page', 1),
            self::LIMIT_PER_PAGE
        );

        return new Response($this->twig->render('pages/job/all_jobs.html.twig', [
            'cities' => $cities,
            'districts' => $districts,
            'jobs' => $jobs,
            'categories' => $categories,
            'category' => $category,
            'user' => $user,
            'cityId' => $cityId,
            'districtId' => $districtId
            //'myArr' => $myArr,
            //'districtList' => $dList
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

            $entityManager = $doctrine->getManager();
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
                'form' => $form->createView()
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
                //$post = $request->request->get('job_form');
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
                'form' => $form->createView()
            ]);
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }
    }

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
        if (!empty($post['week']) && is_array($post['week']) ||
            !empty($post['weekCheck']) && is_array($post['weekCheck'])) {
            foreach ($post['week'] as $key => $week) {
                if ($week['startTime'] !=='' && $week['endTime'] !=='') {
                    $weekArr[] = $week;
                }
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
        if ($post['city'] !=='') {
            $city = $cityRepository->findOneBy(['id' => $post['city']]);
            $job->setCity($city);
        }
        if ($post['district'] !=='') {
            $district = $districtRepository->findOneBy(['id' => $post['district']]);
            $job->setDistrict($district);
        }
        if ($post['task'] !=='' && is_array($post['task'])) {
            foreach ($post['task'] as $taskId) {
                $task = $taskRepository->findOneBy(['id' => $taskId]);
                $job->addTask($task);
                $entityManager->persist($task);
            }
        }
        if ($post['citizenship'] !=='') {
            $citizen = $citizenRepository->findOneBy(['id' => $post['citizenship']]);
            $job->setCitizen($citizen);
        }
        if ($post['accommodation'] !=='') {
            $accommodation = $accommodationRepository->findOneBy(['id' => $post['accommodation']]);
            $job->addAccommodation($accommodation);
        }
        if ($post['busyness'] !=='') {
            $busyness = $busynessRepository->findOneBy(['id' => $post['busyness']]);
            $job->addBusyness($busyness);
        }
        if ($post['additionally'] && is_array($post['additionally'])) {
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
        Job $job,
        JobRepository $jobRepository
    ): Response {
        $category = $job->getCategory();
        $relatedJobs = $jobRepository->findByCategory($category->getId(), $job->getId(), '10');

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
                if (array_key_exists('cheked', $schedule)) {
                    $dataKeys[] = $schedule['cheked'];
                }
            }
        } else {
            $dataKeys = null;
            $scheduleKeys = null;
        }

        return new Response($this->twig->render('pages/job/detail.html.twig', [
            'job' => $job,
            'timesArray' => $timesArray,
            'daysArray' => $daysArray,
            'relatedJobs' => $relatedJobs,
            'schedules' => $schedules,
            'dataKeys' => $dataKeys,
            'scheduleKeys' => $scheduleKeys
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
                    'jobs' => $jobs
                ]);
            }
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }
    }
}
