<?php

namespace App\Service;

use App\Entity\Answer;
use App\Entity\Ticket;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;

class Mailer
{
    private $adminEmail;

    private $mailer;

    public function __construct(
        MailerInterface $mailer,
        Environment     $twig,
        string          $adminEmail
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->adminEmail = $adminEmail;
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendEmail(User $user, string $subject, string $template)
    {
        $date = new \DateTime();
        $email = (new TemplatedEmail())
            ->subject($subject)
            ->htmlTemplate($template)
            ->from($this->adminEmail)
            ->to($user->getEmail())
            ->context([
                'user' => $user,
                'date' => $date
            ]);

        $this->mailer->send($email);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendTicketRequestEmail(User $user, string $subject, string $template, Ticket $ticket)
    {
        $date = new \DateTime();
        $email = (new TemplatedEmail())
            ->subject($subject)
            ->htmlTemplate($template)
            ->from($this->adminEmail)
            ->to($user->getEmail())
            ->context([
                'user' => $user,
                'date' => $date,
                'ticket' => $ticket
            ]);

        $this->mailer->send($email);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendAnswerEmail(User $user, string $subject, string $template, Answer $answer, Ticket $ticket)
    {
        $date = new \DateTime();
        $email = (new TemplatedEmail())
            ->subject($subject)
            ->htmlTemplate($template)
            ->from($this->adminEmail)
            ->to($user->getEmail())
            ->context([
                'user' => $user,
                'date' => $date,
                'answer' => $answer,
                'ticket' => $ticket
            ]);

        $this->mailer->send($email);
    }
}
