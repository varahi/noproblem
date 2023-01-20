<?php

namespace App\Controller\Job;

use App\Repository\BusynessRepository;
use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use App\Repository\DistrictRepository;
use App\Repository\TaskRepository;
use App\Service\ModalForms;
use App\Service\SessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class NewJobController extends AbstractController
{
    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

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
     * @IsGranted("ROLE_EMPLOYEE")
     * @Route("/new-job", name="app_new_job")
     */
    public function newJobAction(
        Request $request,
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        TaskRepository $taskRepository,
        BusynessRepository $busynessRepository,
        CategoryRepository $categoryRepository,
        TranslatorInterface $translator,
        NotifierInterface $notifier
    ): Response {
        if (!$this->isGranted(self::ROLE_EMPLOYEE)) {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_main");
        }

        return $this->render('pages/job/new.html.twig', [
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
