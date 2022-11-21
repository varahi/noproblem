<?php

namespace App\Controller;

use App\ImageOptimizer;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use App\Service\ModalForms;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use App\Entity\Notification as UserNotification;
use Symfony\Component\HttpFoundation\RedirectResponse;

class NotificationController extends AbstractController
{
    /**
     * Time in seconds 3600 - one hour
     */
    public const CACHE_MAX_AGE = '3600';

    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';

    public const ROLE_BUYER = 'ROLE_BUYER';

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
     *
     * @Route("/lk/notifications", name="app_notifications")
     */
    public function notifications(
        Request $request,
        UserRepository $userRepository,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        NotificationRepository $notificationRepository
    ): Response {
        if ($this->isGranted(self::ROLE_EMPLOYEE) || $this->isGranted(self::ROLE_CUSTOMER) || $this->isGranted(self::ROLE_BUYER)) {
            $user = $this->security->getUser();
            {
                $newNotifications = $notificationRepository->findNewByUser($user->getId());
                $viewedNotifications = $notificationRepository->findViewedByUser($user->getId());

                $response = new Response($this->twig->render('user/notifications.html.twig', [
                    'user' => $user,
                    'newNotifications' => $newNotifications,
                    'viewedNotifications' => $viewedNotifications,
                    'ticketForm' => $this->modalForms->ticketForm($request)->createView()
                ]));

                //$response->setSharedMaxAge((int)self::CACHE_MAX_AGE);
                return $response;
            }
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_login");
        }
    }

    /**
     *
     * @Route("/user/notification/mark/id-{id}", name="app_mark_as_read")
     */
    public function markAsRead(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        NotificationRepository $notificationRepository,
        UserNotification $userNotification
    ): Response {
        if ($this->isGranted(self::ROLE_EMPLOYEE) || $this->isGranted(self::ROLE_CUSTOMER) || $this->isGranted(self::ROLE_BUYER)) {
            $user = $this->security->getUser();
            if ($user->getId() == $userNotification->getUser()->getId()) {
                $userNotification->setIsRead('1');
                $entityManager = $this->doctrine->getManager();
                $entityManager->persist($userNotification);
                $entityManager->flush();

                $message = $translator->trans('Notification mark as read', array(), 'flash');
                $notifier->send(new Notification($message, ['browser']));
                $referer = $request->headers->get('referer');
                return new RedirectResponse($referer);
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
