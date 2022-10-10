<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\Worksheet;
use App\Form\Job\JobFormType;
use App\Form\Worksheet\WorksheetFormType;
use App\Repository\JobRepository;
use App\Repository\WorksheetRepository;
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
     * @Route("/job", name="app_job")
     */
    public function index(): Response
    {
        return $this->render('job/index.html.twig', [
            'controller_name' => 'JobController',
        ]);
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
        ManagerRegistry $doctrine
    ): Response {
        if ($this->isGranted(self::ROLE_EMPLOYEE)) {
            $user = $this->security->getUser();

            $job = new Job();
            $form = $this->createForm(JobFormType::class, $job);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $job->setOwner($user);
                $entityManager = $doctrine->getManager();
                $entityManager->persist($job);
                $entityManager->flush();

                $message = $translator->trans('New job created', array(), 'flash');
                $notifier->send(new Notification($message, ['browser']));
                $referer = $request->headers->get('referer');
                return new RedirectResponse($referer);
            }

            return $this->render('job/new.html.twig', [
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
