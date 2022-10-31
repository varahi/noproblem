<?php

namespace App\Controller;

use App\Entity\Worksheet;
use App\Form\Worksheet\WorksheetFormType;
use App\Repository\BusynessRepository;
use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use App\Repository\DistrictRepository;
use App\Repository\ReviewRepository;
use App\Repository\TaskRepository;
use App\Repository\WorksheetRepository;
use App\Service\ModalForms;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
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
use App\Service\FileUploader;

class WorkerController extends AbstractController
{
    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';

    public const ROLE_BUYER = 'ROLE_BUYER';

    private $security;

    public const LIMIT_PER_PAGE = '10';

    /**
     * @param Security $security
     * @param Environment $twig
     * @param ManagerRegistry $doctrine
     * @param ModalForms $modalForms
     */
    public function __construct(
        Security $security,
        Environment $twig,
        ManagerRegistry $doctrine,
        ModalForms $modalForms
    ) {
        $this->security = $security;
        $this->twig = $twig;
        $this->doctrine = $doctrine;
        $this->modalForms = $modalForms;
    }

    /**
     * @Route("/workers", name="app_all_workers")
     */
    public function allWorkers(
        Request $request,
        ReviewRepository $reviewRepository,
        CategoryRepository $categoryRepository,
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        WorksheetRepository $worksheetRepository,
        PaginatorInterface $paginator
    ): Response {
        $slug = $request->query->get('category');
        $cities = $cityRepository->findLimitOrder('999', '0');
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
            //$query = $worksheetRepository->findAll();
            $query = $worksheetRepository->findAllOrder(['created' => 'DESC']);
            $category = null;
            if ($cityId !== '' && $districtId !== '' || $cityId !== '') {
                $query = $worksheetRepository->findByParams($city, $district);
            }
        } else {
            $category = $categoryRepository->findOneBy(['slug' => $slug]);
            $query = $worksheetRepository->findBy(['category' => $category]);
        }

        $worksheets = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::LIMIT_PER_PAGE
        );

        // Get liked ancets
        if ($user != null && count($user->getFeaturedProfiles()) > 0) {
            foreach ($user->getFeaturedProfiles() as $featuredProfile) {
                $featuredProfiles[] = $featuredProfile->getId();
            }
        } else {
            $featuredProfiles = null;
        }

        return new Response($this->twig->render('pages/worksheet/all_workers.html.twig', [
            'cities' => $cities,
            'city' => $city,
            'districts' => $districts,
            'worksheets' => $worksheets,
            'categories' => $categories,
            'category' => $category,
            'user' => $user,
            'cityId' => $cityId,
            'districtId' => $districtId,
            'featuredProfiles' => $featuredProfiles,
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
            //'myArr' => $myArr,
            //'districtList' => $dList
        ]));
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
        ManagerRegistry $doctrine,
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
            $entityManager = $this->doctrine->getManager();

            $worksheet = new Worksheet();
            $form = $this->createForm(WorksheetFormType::class, $worksheet);
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
                $entityManager = $doctrine->getManager();
                $entityManager->persist($worksheet);
                $entityManager->flush();

                $message = $translator->trans('New worksheet created', array(), 'flash');
                $notifier->send(new Notification($message, ['browser']));
                $referer = $request->headers->get('referer');
                return new RedirectResponse($referer);
            }

            return $this->render('pages/worksheet/new.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'cities' => $cities,
                'districts' => $districts,
                'tasks' => $tasks,
                'busynessess' => $busynessess,
                'ticketForm' => $this->modalForms->ticketForm($request)->createView()
            ]);
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
        $post = $request->request->get('worksheet_form');

        if (isset($post['startDate']) && $post['startDate'] !=='') {
            $datetime = new \DateTime();
            $startDate = $datetime->createFromFormat('Y-m-d', $post['startDate']);
            $worksheet->setStartDate($startDate);
        }

        if ($post['city'] !=='') {
            $city = $cityRepository->findOneBy(['id' => $post['city']]);
            $worksheet->setCity($city);
        }
        if ($post['district'] !=='') {
            $district = $districtRepository->findOneBy(['id' => $post['district']]);
            $worksheet->setDistrict($district);
        }
        if ($post['task'] !=='' && is_array($post['task'])) {
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

        $entityManager->persist($worksheet);
        $entityManager->flush();
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
            return $this->render('pages/worksheet/my_worksheets.html.twig', [
                'user' => $user,
                'worksheets' => $worksheets,
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

                if ($form->isSubmitted() && $form->isValid()) {
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
     * @Route("/detail-questionare/questionare-{id}", name="app_detail_worksheet")
     */
    public function worksheetDetailPage(
        Request $request,
        Worksheet $worksheet,
        WorksheetRepository $worksheetRepository
    ): Response {
        $category = $worksheet->getCategory();
        $user = $this->security->getUser();
        $relatedJobs = $worksheetRepository->findByCategory($category->getId(), $worksheet->getId(), '10');

        return new Response($this->twig->render('pages/worksheet/detail.html.twig', [
            'user' => $user,
            'worksheet' => $worksheet,
            'relatedJobs' => $relatedJobs,
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]));
    }

    /**
     *
     * @Route("/selected-profiles", name="app_selected_profiles")
     */
    public function selectedProfiles(
        Request $request,
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        CategoryRepository $categoryRepository,
        WorksheetRepository $worksheetRepository,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        PaginatorInterface $paginator
    ): Response {
        if ($this->isGranted(self::ROLE_EMPLOYEE) || $this->isGranted(self::ROLE_CUSTOMER)) {
            $user = $this->security->getUser();
            //$slug = $request->query->get('category');
            $cities = $cityRepository->findLimitOrder('999', '0');
            $districts = $districtRepository->findAll();
            $categories = $categoryRepository->findAll();

            $cityId = trim($request->query->get('city'));
            $districtId = trim($request->query->get('district'));
            $city = $cityRepository->findOneBy(['id' => $cityId]);
            $district = $districtRepository->findOneBy(['id' => $districtId]);

            /*if ($slug == '') {
                //$query = $worksheetRepository->findAll();
                //$query = $worksheetRepository->findAllOrder(['created' => 'DESC']);
                $query = $worksheetRepository->findSelectedProfiles($user);
                $category = null;
                if ($cityId !== '' && $districtId !== '' || $cityId !== '') {
                    $query = $worksheetRepository->findByParams($city, $district);
                }
            } else {
                $category = $categoryRepository->findOneBy(['slug' => $slug]);
                $query = $worksheetRepository->findBy(['category' => $category]);
            }*/

            $query = $worksheetRepository->findSelectedProfiles($user);

            $worksheets = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                self::LIMIT_PER_PAGE
            );

            $user = $this->security->getUser();

            // Get liked ancets
            if ($user != null && count($user->getFeaturedProfiles()) > 0) {
                foreach ($user->getFeaturedProfiles() as $featuredProfile) {
                    $featuredProfiles[] = $featuredProfile->getId();
                }
            } else {
                $featuredProfiles = null;
            }

            return new Response($this->twig->render('user/selected_profiles.html.twig', [
                'user' => $user,
                'cities' => $cities,
                'city' => $city,
                'districts' => $districts,
                'categories' => $categories,
                //'category' => $category,
                'cityId' => $cityId,
                'districtId' => $districtId,
                'worksheets' => $worksheets,
                'featuredProfiles' => $featuredProfiles,
                //'form' => $form->createView(),
                'ticketForm' => $this->modalForms->ticketForm($request)->createView()
            ]));
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }
    }
}
