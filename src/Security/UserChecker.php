<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if ($user->isIsVerified() == 0) {
            throw new CustomUserMessageAuthenticationException("Ваш аккунт еще не активирован. Пожалуйста перейдите по ссылке в отправленном вам письме!");
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
    }
}
