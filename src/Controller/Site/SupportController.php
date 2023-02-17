<?php

namespace App\Controller\Site;

use App\Entity\Ticket;
use App\Form\Ticket\SupportFormType;
use App\Form\Ticket\TicketFormType;
use App\Service\Mailer;
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
use Twig\Environment;

class SupportController extends AbstractController
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
     * @Route("/support", name="app_support")
     */
    public function index(
        Request $request,
        TranslatorInterface $translator,
        NotifierInterface $notifier,
        Mailer $mailer
    ): Response {
        $user = $this->security->getUser();
        $ticket = new Ticket();

        $url = $this->generateUrl('app_support');
        $ticketForm = $this->createForm(SupportFormType::class, $ticket, [
            'action' => $url,
            'method' => 'POST'
        ]);
        $ticketForm->handleRequest($request);

        if ($ticketForm->isSubmitted() && $ticketForm->isValid()) {

            //$entityManager = $this->doctrine->getManager();
            //$ticket->setUser($user);
            //$ticket->setStatus(0);
            //$entityManager->persist($ticket);
            //$entityManager->flush();

            $subject = $translator->trans('Support request', array(), 'messages');
            $mailer->sendTicketRequestEmail($user, $subject, 'emails/ticket_request.html.twig', $ticket);

            $message = $translator->trans('Ticket created', array(), 'flash');
            $notifier->send(new Notification($message, ['browser']));
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }

        return $this->render('pages/support/index.html.twig', [
            'form' => $ticketForm->createView()
        ]);
    }
}
