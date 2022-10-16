<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Ticket;
use App\Form\Ticket\TicketFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ModalForms extends AbstractController
{
    public function ticketForm(
        Request $request
    ) {
        $ticket = new Ticket();
        $url = $this->generateUrl('app_ticket_new');
        $ticketForm = $this->createForm(TicketFormType::class, $ticket, [
            'action' => $url,
            'method' => 'POST'
        ]);
        $ticketForm->handleRequest($request);

        return $ticketForm;
    }
}
