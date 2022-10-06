<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Http\Request\SignUpRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCreator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function createUser(SignUpRequest $signUpRequest, $role): User
    {
        $user = new User();
        $user
            ->setEmail($signUpRequest->getEmail())
            //->setFullname($signUpRequest->getFullname())
            //->setPassword($this->passwordEncoder->encodePassword($user, $signUpRequest->getPassword()))
            ->setRoles($role)
            ->setIsVerified((bool)'1')
            ->setUsername($signUpRequest->getEmail());

        $encodedPassword = $this->passwordEncoder->encodePassword($user, $signUpRequest->getPassword());
        $user->setPassword($encodedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
