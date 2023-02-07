<?php

namespace App\Controller\Job;

use App\Controller\Traits\AbstractTrait;
use App\Controller\Traits\DataTrait;
use App\Controller\Traits\JobTrait;
use App\Entity\Category;
use App\Entity\Job;
use App\Form\Job\JobFormType;
use App\Form\Job\JobMasterFormType;
use App\Form\Job\JobNannyFormType;
use App\Form\Job\JobNurseFormType;
use App\Form\Job\JobPsychologistFormType;
use App\Form\Job\JobVolounteerFormType;
use App\ImageOptimizer;
use App\Repository\AccommodationRepository;
use App\Repository\AdditionalInfoRepository;
use App\Repository\BusynessRepository;
use App\Repository\CitizenRepository;
use App\Repository\CityRepository;
use App\Repository\DistrictRepository;
use App\Repository\TaskRepository;
use App\Service\CoordinateService;
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
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

class JobPersistent extends AbstractController
{
    use JobTrait;

    use DataTrait;

    use AbstractTrait;

    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';

    public const CATEGORY_PSYCHOLOGIST = '1';

    public const CATEGORY_VOLOUNTEER = '2';

    public const CATEGORY_NANNY = '3';

    public const CATEGORY_NURSE = '4';

    public const CATEGORY_MASTER = '5';


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
     * @Route("/create-job/category-{slug}", name="app_create_job")
     */
    public function createJobAction(
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
        Category $category
    ): Response {
        if (!$this->isGranted(self::ROLE_EMPLOYEE)) {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }

        $user = $this->security->getUser();
        $cities = $cityRepository->findLimitOrder('999', '0');
        $districts = $districtRepository->findLimitOrder('999', '0');
        $tasks = $taskRepository->findAllOrder(['id' => 'ASC']);
        $busynessess = $busynessRepository->findAll();

        $job = new Job();

        if ($category->getId() == self::CATEGORY_PSYCHOLOGIST) {
            $form = $this->createForm(JobPsychologistFormType::class, $job, [
                'action' => $this->generateUrl('app_create_job', ['slug' => $category->getSlug()]),
                'categoryId' => $category->getId(),
                'method' => 'POST'
            ]);
        } elseif ($category->getId() == self::CATEGORY_VOLOUNTEER) {
            $form = $this->createForm(JobVolounteerFormType::class, $job, [
                'action' => $this->generateUrl('app_create_job', ['slug' => $category->getSlug()]),
                'categoryId' => $category->getId(),
                'method' => 'POST'
            ]);
        } elseif ($category->getId() == self::CATEGORY_NANNY) {
            $form = $this->createForm(JobNannyFormType::class, $job, [
                'action' => $this->generateUrl('app_create_job', ['slug' => $category->getSlug()]),
                'categoryId' => $category->getId(),
                'method' => 'POST'
            ]);
        } elseif ($category->getId() == self::CATEGORY_NURSE) {
            $form = $this->createForm(JobNurseFormType::class, $job, [
                'action' => $this->generateUrl('app_create_job', ['slug' => $category->getSlug()]),
                'categoryId' => $category->getId(),
                'method' => 'POST'
            ]);
        } elseif ($category->getId() == self::CATEGORY_MASTER) {
            $form = $this->createForm(JobMasterFormType::class, $job, [
                'action' => $this->generateUrl('app_create_job', ['slug' => $category->getSlug()]),
                'categoryId' => $category->getId(),
                'method' => 'POST'
            ]);
        } else {
            // Redirect
        }

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

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

            // Set coordinates after object saved
            $this->coordinateService->setCoordinates($job);

            $message = $translator->trans('New job created', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }

        if ($category->getId() == self::CATEGORY_PSYCHOLOGIST) {
            $templateName = 'new_psychologist.html.twig';
        } elseif ($category->getId() == self::CATEGORY_VOLOUNTEER) {
            $templateName = 'new_volunteer.html.twig';
        } elseif ($category->getId() == self::CATEGORY_NANNY) {
            $templateName = 'new_nanny.html.twig';
        } elseif ($category->getId() == self::CATEGORY_NURSE) {
            $templateName = 'new_nurse.html.twig';
        } elseif ($category->getId() == self::CATEGORY_MASTER) {
            $templateName = 'new_master.html.twig';
        } else {
            // Redirect
        }

        return $this->render('pages/job/' . $templateName, [
            'user' => $user,
            'form' => $form->createView(),
            'cities' => $cities,
            'districts' => $districts,
            'tasks' => $tasks,
            'busynessess' => $busynessess,
            'cityName' => $this->sessionService->getCity(),
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]);
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
        if (!$this->isGranted(self::ROLE_EMPLOYEE)) {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }

        $user = $this->security->getUser() ?? null;
        // Redirect if not owner
        if ($user->getId() !== $job->getOwner()->getId()) {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }

        $form = $this->createForm(JobFormType::class, $job, [
            'action' => $this->generateUrl('app_edit_job', ['id' => $job->getId()]),
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

        if ($job->getLongitude() !=='' && $job->getLatitude() !=='') {
            $lat = $job->getLatitude();
            $lng = $job->getLongitude();
        } else {
            $lat = $job->getCity()->getLatitude();
            $lng = $job->getCity()->getLongitude();
        }

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

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

            // Set coordinates after object saved
            $this->coordinateService->setCoordinates($job);

            $message = $translator->trans('Job updated', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }

        return $this->render('pages/job/edit.html.twig', [
            'user' => $user,
            'job' => $job,
            'days1' => $days1,
            'days2' => $days2,
            'selectedTasksArr' => $selectedTasksArr,
            'accommodationArr' => $accommodationArr,
            'busynessArr' => $busynessArr,
            'additionalArr' => $additionalArr,
            'lat' => $lat,
            'lng' => $lng,
            'tasks' => $taskRepository->findAllOrder(['id' => 'ASC']),
            'cities' => $cityRepository->findLimitOrder('999', '0'),
            'districts' => $districtRepository->findLimitOrder('999', '0'),
            'citizens' => $citizenRepository->findAllOrder(['id' => 'ASC']),
            'additionals' => $additionalInfoRepository->findAll(),
            'accommodations' => $accommodationRepository->findAll(),
            'busynessess' => $busynessRepository->findAll(),
            'form' => $form->createView(),
            'cityName' => $this->sessionService->getCity(),
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]);
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
        $post = $request->request->get($form->getConfig()->getName());

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

        /*$city = $cityRepository->findOneBy(['name' => $post['city']]);
        if ($city == null) {
            $city = $this->setNewCity($request, 'job_form');
        }
        $job->setCity($city);*/

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
//        if (isset($post['citizenship']) && $post['citizenship'] !=='') {
//            $citizen = $citizenRepository->findOneBy(['id' => $post['citizenship']]);
//            $job->setCitizen($citizen);
//        }
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
        //dd($form->getData());
        if ($form->getData()->getImage() !== null) {
            if ($form->get('image')) {
                $imageFile = $form->get('image')->getData();
                if ($imageFile) {
                    $imageFileName = $fileUploader->upload($imageFile);
                    $job->setImage($imageFileName);
                }
            }
        }

        // Passport file upload
        /*$passportPhoto = $form->get('passportPhoto')->getData();
        if ($passportPhoto) {
            $passportFileName = $fileUploader->upload($passportPhoto);
            $job->setPassportPhoto($passportFileName);
        }*/

        $entityManager->persist($job);
        $entityManager->flush();
    }
}
