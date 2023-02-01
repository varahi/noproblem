<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Ticket;
use App\Form\Ticket\TicketFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class ModalForms extends AbstractController
{
    public function __construct(
        Security $security
    ) {
        $this->security = $security;
    }

    public function ticketForm(
        Request $request
    ) {
        $ticket = new Ticket();

        if ($this->security->getUser() !== null) {
            $url = $this->generateUrl('app_ticket_new');
        } else {
            $url = $this->generateUrl('app_feedback_new');
        }

        $ticketForm = $this->createForm(TicketFormType::class, $ticket, [
            'action' => $url,
            'method' => 'POST'
        ]);
        $ticketForm->handleRequest($request);

        return $ticketForm;
    }
}
