<?php

namespace App\Controller\Work;

use App\Controller\Traits\AbstractTrait;
use App\Controller\Traits\DataTrait;
use App\Controller\Traits\JobTrait;
use App\Entity\Category;
use App\Entity\Worksheet;
use App\Form\Worksheet\WorksheetFormType;
use App\Form\Worksheet\WorksheetMasterFormType;
use App\Form\Worksheet\WorksheetNannyFormType;
use App\Form\Worksheet\WorksheetNurseFormType;
use App\Form\Worksheet\WorksheetPsychologistFormType;
use App\Form\Worksheet\WorksheetVolounteerFormType;
use App\Repository\BusynessRepository;
use App\Repository\CategoryRepository;
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

final class WorkPersistent extends AbstractController
{
    use DataTrait;

    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';

    public const ROLE_BUYER = 'ROLE_BUYER';

    public const CATEGORY_PSYCHOLOGIST = '1';

    public const CATEGORY_VOLOUNTEER = '2';

    public const CATEGORY_NANNY = '3';

    public const CATEGORY_NURSE = '4';

    public const CATEGORY_MASTER = '5';

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
     *
     * @IsGranted("ROLE_CUSTOMER")
     * @Route("/create-worksheet/category-{slug}", name="app_create_worksheet")
     */
    public function createWorksheet(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        TaskRepository $taskRepository,
        BusynessRepository $busynessRepository,
        CategoryRepository $categoryRepository,
        FileUploader $fileUploader,
        Category $category
    ): Response {
        if (!$this->isGranted(self::ROLE_CUSTOMER)) {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }

        $user = $this->security->getUser();
        $cities = $cityRepository->findLimitOrder('999', '0');
        $districts = $districtRepository->findLimitOrder('999', '0');
        $tasks = $taskRepository->findAllOrder(['id' => 'ASC']);
        $busynessess = $busynessRepository->findAll();

        $worksheet = new Worksheet();

        if ($category->getId() == self::CATEGORY_PSYCHOLOGIST) {
            $form = $this->createForm(WorksheetPsychologistFormType::class, $worksheet, [
                'action' => $this->generateUrl('app_create_worksheet', ['slug' => $category->getSlug()]),
                'categoryId' => $category->getId(),
                'method' => 'POST'
            ]);
        } elseif ($category->getId() == self::CATEGORY_VOLOUNTEER) {
            $form = $this->createForm(WorksheetVolounteerFormType::class, $worksheet, [
                'action' => $this->generateUrl('app_create_worksheet', ['slug' => $category->getSlug()]),
                'categoryId' => $category->getId(),
                'method' => 'POST'
            ]);
        } elseif ($category->getId() == self::CATEGORY_NANNY) {
            $form = $this->createForm(WorksheetNannyFormType::class, $worksheet, [
                'action' => $this->generateUrl('app_create_worksheet', ['slug' => $category->getSlug()]),
                'categoryId' => $category->getId(),
                'method' => 'POST'
            ]);
        } elseif ($category->getId() == self::CATEGORY_NURSE) {
            $form = $this->createForm(WorksheetNurseFormType::class, $worksheet, [
                'action' => $this->generateUrl('app_create_worksheet', ['slug' => $category->getSlug()]),
                'categoryId' => $category->getId(),
                'method' => 'POST'
            ]);
        } elseif ($category->getId() == self::CATEGORY_MASTER) {
            $form = $this->createForm(WorksheetMasterFormType::class, $worksheet, [
                'action' => $this->generateUrl('app_create_worksheet', ['slug' => $category->getSlug()]),
                'categoryId' => $category->getId(),
                'method' => 'POST'
            ]);
        } else {
            // Redirect
        }

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->updateFields(
                $request,
                $form,
                $worksheet,
                $cityRepository,
                $districtRepository,
                $taskRepository,
                $fileUploader
            );

            $worksheet->setUser($user);
            $worksheet->setHidden('0');
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($worksheet);
            $entityManager->flush();

            // Set coordinates after object saved
            $this->coordinateService->setCoordinates($worksheet);

            $message = $translator->trans('New worksheet created', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_new_work");
            //$referer = $request->headers->get('referer');
            //return new RedirectResponse($referer);
        }

        if ($category->getId() == self::CATEGORY_PSYCHOLOGIST) {
            return $this->render('pages/worksheet/new_psychologist.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'cities' => $cities,
                'districts' => $districts,
                'tasks' => $tasks,
                'busynessess' => $busynessess,
                'cityName' => $this->sessionService->getCity(),
                'ticketForm' => $this->modalForms->ticketForm($request)->createView()
            ]);
        } elseif ($category->getId() == self::CATEGORY_VOLOUNTEER) {
            return $this->render('pages/worksheet/new_volunteer.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'cities' => $cities,
                'districts' => $districts,
                'tasks' => $tasks,
                'busynessess' => $busynessess,
                'cityName' => $this->sessionService->getCity(),
                'ticketForm' => $this->modalForms->ticketForm($request)->createView()
            ]);
        } elseif ($category->getId() == self::CATEGORY_NANNY) {
            return $this->render('pages/worksheet/new_nanny.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'cities' => $cities,
                'districts' => $districts,
                'tasks' => $tasks,
                'busynessess' => $busynessess,
                'cityName' => $this->sessionService->getCity(),
                'ticketForm' => $this->modalForms->ticketForm($request)->createView()
            ]);
        } elseif ($category->getId() == self::CATEGORY_NURSE) {
            return $this->render('pages/worksheet/new_nurse.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'cities' => $cities,
                'districts' => $districts,
                'tasks' => $tasks,
                'busynessess' => $busynessess,
                'cityName' => $this->sessionService->getCity(),
                'ticketForm' => $this->modalForms->ticketForm($request)->createView()
            ]);
        } elseif ($category->getId() == self::CATEGORY_MASTER) {
            return $this->render('pages/worksheet/new_master.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'cities' => $cities,
                'districts' => $districts,
                'tasks' => $tasks,
                'busynessess' => $busynessess,
                'cityName' => $this->sessionService->getCity(),
                'ticketForm' => $this->modalForms->ticketForm($request)->createView()
            ]);
        } else {
            // Redirect
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
        Worksheet $worksheet,
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        TaskRepository $taskRepository,
        BusynessRepository $busynessRepository,
        FileUploader $fileUploader
    ): Response {
        if ($this->isGranted(self::ROLE_CUSTOMER)) {
            $user = $this->security->getUser();
            $cities = $cityRepository->findLimitOrder('999', '0');
            $districts = $districtRepository->findLimitOrder('999', '0');
            $tasks = $taskRepository->findAllOrder(['id' => 'ASC']);
            $busynessess = $busynessRepository->findAll();

            if ($user->getId() == $worksheet->getUser()->getId()) {
                $form = $this->createForm(WorksheetFormType::class, $worksheet);
                $form->handleRequest($request);

                // Get selected checkboxes
                if (count($worksheet->getTasks()) > 0) {
                    foreach ($worksheet->getTasks() as $task) {
                        $selectedTasksArr[] = $task->getId();
                    }
                } else {
                    $selectedTasksArr = null;
                }

                if (count($worksheet->getBusynesses()) > 0) {
                    foreach ($worksheet->getBusynesses() as $busyness) {
                        $busynessArr[] = $busyness->getId();
                    }
                } else {
                    $busynessArr = null;
                }

                if ($worksheet->getLongitude() !='' && $worksheet->getLatitude() !=='') {
                    $lat = $worksheet->getLatitude();
                    $lng = $worksheet->getLongitude();
                } else {
                    $lat = $worksheet->getCity()->getLatitude();
                    $lng = $worksheet->getCity()->getLongitude();
                }

                if ($form->isSubmitted()) {
                    $this->updateFields(
                        $request,
                        $form,
                        $worksheet,
                        $cityRepository,
                        $districtRepository,
                        $taskRepository,
                        $fileUploader
                    );

                    // Set coordinates after object saved
                    $this->coordinateService->setCoordinates($worksheet);

                    $entityManager = $doctrine->getManager();
                    $entityManager->persist($worksheet);
                    $entityManager->flush();

                    $message = $translator->trans('Worksheet updated', array(), 'flash');
                    $notifier->send(new Notification($message, ['browser']));
                    $referer = $request->headers->get('referer');
                    return new RedirectResponse($referer);
                }
                return new Response($this->twig->render('pages/worksheet/edit.html.twig', [
                    'user' => $user,
                    'cities' => $cities,
                    'districts' => $districts,
                    'tasks' => $tasks,
                    'busynessess' => $busynessess,
                    'selectedTasksArr' => $selectedTasksArr,
                    'busynessArr' => $busynessArr,
                    'form' => $form->createView(),
                    'worksheet' => $worksheet,
                    'cityName' => $this->sessionService->getCity(),
                    'lat' => $lat,
                    'lng' => $lng,
                    'ticketForm' => $this->modalForms->ticketForm($request)->createView()
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
     * @param Request $request
     * @param $form
     * @param Worksheet $worksheet
     * @param CityRepository $cityRepository
     * @param DistrictRepository $districtRepository
     * @param TaskRepository $taskRepository
     * @param FileUploader $fileUploader
     * @return void
     */
    private function updateFields(
        Request $request,
        $form,
        Worksheet $worksheet,
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        TaskRepository $taskRepository,
        FileUploader $fileUploader
    ) {
        $entityManager = $this->doctrine->getManager();
        //$post = $request->request->get('worksheet_form');
        $post = $request->request->get($form->getConfig()->getName());

        if (isset($post['startDate']) && $post['startDate'] !=='') {
            $datetime = new \DateTime();
            $startDate = $datetime->createFromFormat('Y-m-d', $post['startDate']);
            $worksheet->setStartDate($startDate);
        }

        /*if ($post['city'] !=='') {
            $city = $cityRepository->findOneBy(['id' => $post['city']]);
            $worksheet->setCity($city);
        }*/

        $city = $cityRepository->findOneBy(['name' => $post['city']]);
        if ($city == null) {
            $city = $this->setNewCity($request, 'worksheet_form');
        }

        $worksheet->setCity($city);

        if (isset($post['district']) && $post['district'] !=='') {
            $district = $districtRepository->findOneBy(['id' => $post['district']]);
            $worksheet->setDistrict($district);
        }
        if (isset($post['task']) && $post['task'] !=='' && is_array($post['task'])) {
            foreach ($post['task'] as $taskId) {
                $task = $taskRepository->findOneBy(['id' => $taskId]);
                $worksheet->addTask($task);
                $entityManager->persist($task);
            }
        }

        // File upload
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $imageFileName = $fileUploader->upload($imageFile);
            $worksheet->setImage($imageFileName);
        }

        // Passport file upload
        /*$passportPhoto = $form->get('passportPhoto')->getData();
        if ($passportPhoto) {
            $passportFileName = $fileUploader->upload($passportPhoto);
            $worksheet->setPassportPhoto($passportFileName);
        }*/

        $entityManager->persist($worksheet);
        $entityManager->flush();
    }
}
