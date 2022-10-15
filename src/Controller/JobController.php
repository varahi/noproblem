<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\Worksheet;
use App\Form\Job\JobFormType;
use App\Form\Worksheet\WorksheetFormType;
use App\Repository\AccommodationRepository;
use App\Repository\AdditionalInfoRepository;
use App\Repository\BusynessRepository;
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

class JobController extends AbstractController
{
    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';

    public const ROLE_BUYER = 'ROLE_BUYER';

    private $security;

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
                1 => 'Пн',
                2 => 'Вт',
                3 => 'Ср',
                4 => 'Чт',

            ];
            $daysArr2 = [
                5 => 'Пт',
                6 => 'Сб',
                7 => 'Вс',
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

                // Week days start time and end time
                if (!empty($post['week']) && is_array($post['week']) && !empty($post['weekCheck']) && is_array($post['weekCheck'])) {
                    foreach ($post['week'] as $key => $week) {
                        if ($week['startTime'] !=='' && $week['endTime'] !=='') {
                            $weekArr[] = $week;
                        }
                    }
                }
                // Checked week days
                if (!empty($post['weekCheck']) && is_array($post['weekCheck'])) {
                    foreach ($post['weekCheck'] as $weekCheck) {
                        $weekCheckArr[]['cheked'] = $weekCheck;
                    }
                }

                // Generate json to put in database
                if (isset($weekArr) && isset($weekCheckArr)) {
                    $scheduleArr = array_merge($weekArr, $weekCheckArr);
                    $scheduleArrJson = json_encode($scheduleArr);
                } elseif (isset($weekCheckArr)) {
                    $scheduleArrJson = json_encode($weekCheckArr);
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

                $message = $translator->trans('New job created', array(), 'flash');
                $notifier->send(new Notification($message, ['browser']));
                $referer = $request->headers->get('referer');
                return new RedirectResponse($referer);
            }

            return $this->render('job/new.html.twig', [
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
     * @Route("/detail-job", name="app_all_jobs")
     */
    public function allJobs(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        ManagerRegistry $doctrine,
        Job $job
    ): Response {
    }

    /**
     *
     * @IsGranted("ROLE_EMPLOYEE")
     * @Route("/detail-job/job-{id}", name="app_detail_job")
     */
    public function detailJob(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        ManagerRegistry $doctrine,
        Job $job,
        JobRepository $jobRepository
    ): Response {
        /*if ($this->isGranted(self::ROLE_EMPLOYEE)) {
            $user = $this->security->getUser();
            if ($user->getId() == $job->getOwner()->getId()) {

            }
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }*/

        $category = $job->getCategory();
        $relatedJobs = $jobRepository->findByCategory($category->getId(), '10');

        return new Response($this->twig->render('job/detail.html.twig', [
            'job' => $job,
            'relatedJobs' => $relatedJobs
        ]));
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
        Job $job
    ): Response {
        if ($this->isGranted(self::ROLE_EMPLOYEE)) {
            $user = $this->security->getUser();
            $skills = [
                'Нет' => null,
                'Менее 6 месяцев' => '1',
                '6-12 месяцев' => '2',
                '2-5 лет' => '3',
                'более 5 лет' => '4',
            ];

            if ($user->getId() == $job->getOwner()->getId()) {
                $form = $this->createForm(JobFormType::class, $job);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $entityManager = $doctrine->getManager();
                    $entityManager->persist($job);
                    $entityManager->flush();

                    $message = $translator->trans('Job updated', array(), 'flash');
                    $notifier->send(new Notification($message, ['browser']));
                    $referer = $request->headers->get('referer');
                    return new RedirectResponse($referer);
                }
                return new Response($this->twig->render('job/edit.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
                    'skills' => $skills,
                    'job' => $job
                ]));
            } else {
                $message = $translator->trans('Please login', array(), 'flash');
                $notifier->send(new Notification($message, ['browser']));
                return $this->redirectToRoute("app_main");
            }
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }
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
                return $this->render('job/my_jobs.html.twig', [
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

    /**
     *
     * @IsGranted("ROLE_CUSTOMER")
     * @Route("/new-worksheet", name="app_new_worksheet")
     */
    public function newWorksheet(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        ManagerRegistry $doctrine
    ): Response {
        if ($this->isGranted(self::ROLE_CUSTOMER)) {
            $user = $this->security->getUser();
            $worksheet = new Worksheet();
            $form = $this->createForm(WorksheetFormType::class, $worksheet);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($_POST['startDate'] !== '') {
                    $datetime = new \DateTime();
                    $startDate = $datetime->createFromFormat('Y-m-d', $_POST['startDate']);
                    $worksheet->setStartDate($startDate);
                }
                $worksheet->setUser($user);
                $worksheet->setHidden('0');
                $entityManager = $doctrine->getManager();
                $entityManager->persist($worksheet);
                $entityManager->flush();

                $message = $translator->trans('New worksheet created', array(), 'flash');
                $notifier->send(new Notification($message, ['browser']));
                $referer = $request->headers->get('referer');
                return new RedirectResponse($referer);
            }

            return $this->render('worksheet/new.html.twig', [
                'user' => $user,
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
     * @IsGranted("ROLE_CUSTOMER")
     * @Route("/my-worksheets", name="app_my_worksheets")
     */
    public function myWorksheet(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        ManagerRegistry $doctrine,
        WorksheetRepository $worksheetRepository
    ): Response {
        if ($this->isGranted(self::ROLE_CUSTOMER)) {
            $user = $this->security->getUser();
            $worksheets = $worksheetRepository->findByUser($user->getId());
            return $this->render('worksheet/my_worksheets.html.twig', [
                'user' => $user,
                'worksheets' => $worksheets
            ]);
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }
    }

    /**
     *
     * @IsGranted("ROLE_CUSTOMER")
     * @Route("/edit-worksheet/worksheet-{id}", name="app_edit_worksheet")
     */
    public function editWorksheet(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        ManagerRegistry $doctrine,
        Worksheet $worksheet
    ): Response {
        if ($this->isGranted(self::ROLE_CUSTOMER)) {
            $user = $this->security->getUser();
            if ($user->getId() == $worksheet->getUser()->getId()) {
                $form = $this->createForm(WorksheetFormType::class, $worksheet);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $entityManager = $doctrine->getManager();
                    $entityManager->persist($worksheet);
                    $entityManager->flush();

                    $message = $translator->trans('Worksheet updated', array(), 'flash');
                    $notifier->send(new Notification($message, ['browser']));
                    $referer = $request->headers->get('referer');
                    return new RedirectResponse($referer);
                }
                return new Response($this->twig->render('worksheet/edit.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
                    'worksheet' => $worksheet
                ]));
            } else {
                $message = $translator->trans('Please login', array(), 'flash');
                $notifier->send(new Notification($message, ['browser']));
                return $this->redirectToRoute("app_main");
            }
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }
    }
}
