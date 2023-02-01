<?php

namespace App\Controller;

use App\Form\Answer\AnswerFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Ticket;
use App\Entity\Answer;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Service\Mailer;
use App\Form\Ticket\TicketFormType;

class TicketController extends AbstractController
{
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    public const ROLE_EMPLOYEE = 'ROLE_EMPLOYEE';

    public const ROLE_CUSTOMER = 'ROLE_CUSTOMER';

    public const ROLE_BUYER = 'ROLE_BUYER';

    public const STATUS_NEW = '0';

    public const STATUS_ACTIVE = '1';

    public const STATUS_COMPLETED = '9';

    private $doctrine;

    private $security;

    private $twig;

    /**
     * @param Security $security
     * @param Environment $twig
     * @param ManagerRegistry $doctrine
     */
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
     *
     * @Route("/ticket-list", name="app_ticket_list")
     */
    public function list(
        TicketRepository $ticketRepository,
        NotifierInterface $notifier,
        TranslatorInterface $translator
    ): Response {
        if ($this->isGranted(self::ROLE_SUPER_ADMIN)) {
            $user = $this->security->getUser();

            $newTickets = $ticketRepository->findAllByStatus(self::STATUS_NEW);
            $activeTickets = $ticketRepository->findAllByStatus(self::STATUS_ACTIVE);
            $completedTickets = $ticketRepository->findAllByStatus(self::STATUS_COMPLETED);

            return $this->render('ticket/ticket_list.html.twig', [
                'user' => $user,
                'newTickets' => $newTickets,
                'activeTickets' => $activeTickets,
                'completedTickets' => $completedTickets,
            ]);
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_login");
        }
    }

    /**
     * @Route("/support/new-ticket", name="app_ticket_new")
     */
    public function newTicket(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        ManagerRegistry $doctrine,
        Mailer $mailer,
        UserRepository $userRepository
    ): Response {
        $user = $this->security->getUser();
        $ticket = new Ticket();

        $url = $this->generateUrl('app_ticket_new');
        $ticketForm = $this->createForm(TicketFormType::class, $ticket, [
            'action' => $url,
            'method' => 'POST'
        ]);
        $ticketForm->handleRequest($request);

        if ($ticketForm->isSubmitted() && $ticketForm->isValid()) {
            $entityManager = $doctrine->getManager();
            $ticket->setUser($user);
            $ticket->setStatus(0);
            $entityManager->persist($ticket);
            $entityManager->flush();
            $subject = $translator->trans('Support request', array(), 'messages');
            $mailer->sendTicketRequestEmail($user, $subject, 'emails/ticket_request.html.twig', $ticket);

            $message = $translator->trans('Ticket created', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }

        return $this->render('ticket/new_ticket.html.twig', [
            'user' => $user,
            'form' => $ticketForm->createView()
        ]);
    }

    /**
     * @Route("/edit-ticket/ticket-{id}", name="app_edit_ticket")
     */
    public function editTicket(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        Ticket $ticket,
        ManagerRegistry $doctrine,
        Mailer $mailer
    ): Response {
        if ($this->isGranted(self::ROLE_SUPER_ADMIN)) {
            $user = $this->security->getUser();

            $answer = new Answer();
            $form = $this->createForm(AnswerFormType::class, $answer);
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $post = $request->request->get('answer_form');
                $entityManager = $doctrine->getManager();
                $ticket->setStatus(self::STATUS_ACTIVE);
                $answer->setTicket($ticket);

                // Close ticket
                if (isset($post['closeTicket']) && $post['closeTicket'] !=='') {
                    $ticket->setClosed(new \DateTime());
                    $ticket->setStatus(self::STATUS_COMPLETED);
                }

                // Set answered user
                $answer->setUser($user);
                $entityManager->persist($answer);
                $entityManager->persist($ticket);
                $entityManager->flush();

                $subject = $translator->trans('Your request has been answered', array(), 'messages');
                $mailer->sendAnswerEmail($ticket->getUser(), $subject, 'emails/answer_ticket_to_user.html.twig', $answer, $ticket);

                $message = $translator->trans('Answered', array(), 'flash');
                $notifier->send(new Notification($message, ['browser']));
                return $this->redirectToRoute("app_ticket_list");
            }

            return $this->render('ticket/ticket_edit.html.twig', [
                'ticket' => $ticket,
                'answerForm' => $form->createView()
            ]);
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_login");
        }
    }

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/activate-ticket/ticket-{id}", name="app_activate_ticket")
     */
    public function activateTicket(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        Ticket $ticket,
        ManagerRegistry $doctrine
    ) {
        if ($this->isGranted(self::ROLE_SUPER_ADMIN)) {
            $entityManager = $doctrine->getManager();
            $ticket->setStatus(self::STATUS_ACTIVE);
            $entityManager->persist($ticket);
            $entityManager->flush();

            $message = $translator->trans('Ticket activated again', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        } else {
            $message = $translator->trans('Please login', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            return $this->redirectToRoute("app_login");
        }
    }

    /**
     * @Route("/support/new-feedback", name="app_feedback_new")
     */
    public function newFeedback(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        ManagerRegistry $doctrine,
        Mailer $mailer
    ): Response {
        $ticket = new Ticket();
        $url = $this->generateUrl('app_ticket_new');
        $ticketForm = $this->createForm(TicketFormType::class, $ticket, [
            'action' => $url,
            'method' => 'POST'
        ]);
        $ticketForm->handleRequest($request);

        if ($ticketForm->isSubmitted() && $ticketForm->isValid()) {
            if (!filter_var($_POST['ticket_form']['email'], FILTER_VALIDATE_EMAIL)) {
                $message = $translator->trans('Invalid email', array(), 'flash');
                $notifier->send(new Notification($message, ['browser']));
                $referer = $request->headers->get('referer');
                return new RedirectResponse($referer);
            }

            $entityManager = $doctrine->getManager();
            //$ticket->setUser($user);
            $ticket->setStatus(0);
            $entityManager->persist($ticket);
            $entityManager->flush();

            $subject = $translator->trans('Support request', array(), 'messages');
            $mailer->sendFeedbackRequestEmail($_POST['ticket_form']['email'], $subject, 'emails/feedback_request.html.twig', $ticket);
            $message = $translator->trans('Ticket created', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }

        return $this->render('ticket/new_ticket.html.twig', [
            'form' => $ticketForm->createView()
        ]);
    }
}
