<?php

namespace App\Controller;

use App\Entity\Worksheet;
use App\Form\Worksheet\WorksheetFormType;
use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use App\Repository\DistrictRepository;
use App\Repository\ReviewRepository;
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
            $query = $worksheetRepository->findAll();
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
                'form' => $form->createView(),
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
        $relatedJobs = $worksheetRepository->findByCategory($category->getId(), $worksheet->getId(), '10');

        return new Response($this->twig->render('pages/worksheet/detail.html.twig', [
            'worksheet' => $worksheet,
            'relatedJobs' => $relatedJobs,
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]));
    }
}
