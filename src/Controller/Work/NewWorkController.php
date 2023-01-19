<?php

namespace App\Controller\Work;

use App\Repository\BusynessRepository;
use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use App\Repository\DistrictRepository;
use App\Repository\TaskRepository;
use App\Service\ModalForms;
use App\Service\SessionService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

class NewWorkController extends AbstractController
{
    /**
     * @param Security $security
     * @param Environment $twig
     * @param ManagerRegistry $doctrine
     * @param ModalForms $modalForms
     * @param SessionService $sessionService
     */
    public function __construct(
        Security $security,
        ModalForms $modalForms,
        SessionService $sessionService
    ) {
        $this->security = $security;
        $this->modalForms = $modalForms;
        $this->sessionService = $sessionService;
    }

    /**
     *
     * @IsGranted("ROLE_CUSTOMER")
     * @Route("/new-work", name="app_new_work")
     */
    public function newWork(
        Request $request,
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        TaskRepository $taskRepository,
        BusynessRepository $busynessRepository,
        CategoryRepository $categoryRepository
    ): Response {
        return $this->render('pages/worksheet/new.html.twig', [
            'user' => $this->security->getUser() ?? null,
            'cities' => $cityRepository->findLimitOrder('999', '0'),
            'districts' => $districtRepository->findLimitOrder('999', '0'),
            'tasks' => $taskRepository->findAllOrder(['id' => 'ASC']),
            'busynessess' =>  $busynessRepository->findAll(),
            'categories' => $categoryRepository->findLimitOrder('10', '0'),
            'cityName' => $this->sessionService->getCity(),
            'ticketForm' => $this->modalForms->ticketForm($request)->createView()
        ]);
    }
}
