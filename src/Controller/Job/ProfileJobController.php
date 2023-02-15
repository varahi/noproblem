<?php

namespace App\Controller\Job;

use App\Controller\Traits\AbstractTrait;
use App\Controller\Traits\DataTrait;
use App\Controller\Traits\JobTrait;
use App\Entity\Job;
use App\ImageOptimizer;
use App\Repository\CityRepository;
use App\Repository\JobRepository;
use App\Service\CoordinateService;
use App\Service\ModalForms;
use App\Service\SessionService;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ProfileJobController extends AbstractController
{
    use JobTrait;

    use DataTrait;

    use AbstractTrait;

    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';

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
            $user = $this->security->getUser() ?? null;
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
            $user = $this->security->getUser() ?? null;


            /*$query = $jobRepository->findSelectedProfiles($user);
            $jobs = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                self::LIMIT_PER_PAGE
            );*/

            return new Response($this->twig->render('user/selected_jobs.html.twig', [
                'user' => $user,
                'jobs' => $jobRepository->findSelectedProfiles($user),
                'featuredJobs' => $this->getFeaturedJobs($user),
                'cityName' => $this->sessionService->getCity(),
                'ticketForm' => $this->modalForms->ticketForm($request)->createView()
            ]));
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }
    }

    /**
     *
     * @Route("/detail-job/job-{id}", name="app_detail_job")
     */
    public function detailJob(
        Request $request,
        Job $job,
        JobRepository $jobRepository,
        CityRepository $cityRepository
    ): Response {
        $category = $job->getCategory();
        $user = $this->security->getUser() ?? null;

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

        // Set coords for worksheet
        $this->coordinateService->setCoordinates($job);

        return new Response($this->twig->render('pages/job/detail.html.twig', [
            'user' => $user,
            'job' => $job,
            'timesArray' => $timesArray,
            'daysArray' => $daysArray,
            'schedules' => $schedules,
            'dayKeys' => $dayKeys,
            'scheduleKeys' => $scheduleKeys,
            'testArray' => $testArray,
            'relatedJobs' => $jobRepository->findByCategory($category->getId(), $job->getId(), '10'),
            'featuredJobs' => $this->getFeaturedJobs($user),
            'cityName' => $this->sessionService->getCity(),
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]));
    }
}
