<?php

namespace App\Security;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class UserChecker implements UserCheckerInterface
{
    private EmailVerifier $emailVerifier;

    private $translator;

    public function __construct(
        EmailVerifier $emailVerifier,
        TranslatorInterface $translator,
        VerifyEmailHelperInterface $helper
    ) {
        $this->emailVerifier = $emailVerifier;
        $this->translator = $translator;
        $this->verifyEmailHelper = $helper;
    }
    public function checkPreAuth(UserInterface $user)
    {
        $message = $this->translator->trans('Your account has not been activated yet', array(), 'flash');
        if ($user->isIsVerified() == 0) {
            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()] // add the user's id as an extra query param
            );

            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('webmaster@noproblem.ru', 'Admin'))
                    ->to($user->getEmail())
                    ->subject($this->translator->trans('Please Confirm your Email', array(), 'message'))
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                    ->context([
                        'verifyUrl' => $signatureComponents->getSignedUrl()
                    ])
            );

            throw new CustomUserMessageAuthenticationException($message);
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
    }
}
