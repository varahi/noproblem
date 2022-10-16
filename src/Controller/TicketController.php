<?php

namespace App\Controller;

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
     * @Route("/ticket", name="app_ticket")
     */
    public function index(): Response
    {
        return $this->render('ticket/index.html.twig', [
            'controller_name' => 'TicketController',
        ]);
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

        $form = $this->createForm(TicketFormType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $ticket->setUser($user);
            $ticket->setStatus(0);
            $entityManager->persist($ticket);
            $entityManager->flush();

            $subject = $translator->trans('Support request', array(), 'messages');
            if (isset($adminUsers)) {
                foreach ($adminUsers as $adminUser) {
                    $mailer->sendTicketRequestEmail($adminUser, $subject, 'emails/ticket_request.html.twig', $ticket);
                }
            }

            $message = $translator->trans('Ticket created', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }

        return $this->render('ticket/new_ticket.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}
