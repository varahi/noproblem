<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Http\Request\SignUpRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Security\EmailVerifier;
use App\Service\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class UserCreator extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    private $emailVerifier;

    private $mailer;

    private $adminEmail;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        EmailVerifier $emailVerifier,
        VerifyEmailHelperInterface $helper,
        MailerInterface $mailer,
        string $adminEmail
    ) {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->emailVerifier = $emailVerifier;
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
        $this->adminEmail = $adminEmail;
    }

    public function createUser(SignUpRequest $signUpRequest, $role): User
    {
        $user = new User();
        $user
            ->setEmail($signUpRequest->getEmail())
            //->setFullname($signUpRequest->getFullname())
            //->setPassword($this->passwordEncoder->encodePassword($user, $signUpRequest->getPassword()))
            ->setRoles($role)
            ->setIsVerified((bool)'0')
            ->setUsername($signUpRequest->getEmail());

        $encodedPassword = $this->passwordEncoder->encodePassword($user, $signUpRequest->getPassword());
        $user->setPassword($encodedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('webmaster@noproblem.ru', 'Admin'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/_confirmation_email_back.html.twig')
        );

        // Verify email
        /*$signatureComponents = $this->verifyEmailHelper->generateSignature(
            'app_verify_email',
            $user->getId(),
            $user->getEmail(),
            ['id' => $user->getId()] // add the user's id as an extra query param
        );

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('noreply@smcentr.su', 'Admin'))
                ->to($user->getEmail())
                ->subject('Пожалуйста подтвердите ваш аккаунт')
                ->htmlTemplate('registration/confirmation_email.html.twig')
                ->context([
                    'verifyUrl' => $signatureComponents->getSignedUrl()
                ])
        );*/

        return $user;
    }
}
