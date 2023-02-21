<?php

namespace App\Controller\Work;

use App\Controller\Traits\AbstractTrait;
use App\Controller\Traits\DataTrait;
use App\Controller\Traits\JobTrait;
use App\Entity\Worksheet;
use App\Repository\CategoryRepository;
use App\Repository\CitizenRepository;
use App\Repository\CityRepository;
use App\Repository\DistrictRepository;
use App\Repository\ReviewRepository;
use App\Repository\TaskRepository;
use App\Repository\WorksheetRepository;
use App\Service\ModalForms;
use App\Service\SessionService;
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
use App\Service\CoordinateService;

class ProfileWorkController extends AbstractController
{
    use JobTrait;

    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';

    public const ROLE_BUYER = 'ROLE_BUYER';

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
     * @Route("/detail/worksheet-{id}", name="app_detail_worksheet")
     */
    public function worksheetDetailPage(
        Request $request,
        Worksheet $worksheet,
        WorksheetRepository $worksheetRepository,
        CityRepository $cityRepository
    ): Response {
        $category = $worksheet->getCategory();
        $user = $this->security->getUser();

        // Get liked worksheet
        $featuredProfiles = $this->getFeaturedProfiles($user);
        // Set coords for worksheet
        $this->coordinateService->setCoordinates($worksheet);

        return new Response($this->twig->render('pages/worksheet/detail.html.twig', [
            'user' => $user,
            'worksheet' => $worksheet,
            'relatedJobs' => $worksheetRepository->findByCategory($category?->getId(), $worksheet->getId(), '10') ?? null,
            'featuredProfiles' => $featuredProfiles,
            'cityName' => $this->sessionService->getCity(),
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

            /*$query = $worksheetRepository->findSelectedProfiles($user);
            $worksheets = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                self::LIMIT_PER_PAGE
            );*/

            $worksheets = $worksheetRepository->findSelectedProfiles($user);
            $user = $this->security->getUser();
            $featuredProfiles = $this->getFeaturedProfiles($user);

            return new Response($this->twig->render('user/selected_profiles.html.twig', [
                'user' => $user,
                'cities' => $cities,
                'city' => $city,
                'districts' => $districts,
                'categories' => $categories,
                'cityId' => $cityId,
                'districtId' => $districtId,
                'worksheets' => $worksheets,
                'featuredProfiles' => $featuredProfiles,
                'cityName' => $this->sessionService->getCity(),
                'lat' => $this->coordinateService->getLatArr($worksheets, $city),
                'lng' => $this->coordinateService->getLngArr($worksheets, $city),

                'ticketForm' => $this->modalForms->ticketForm($request)->createView()
            ]));
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }
    }
}
