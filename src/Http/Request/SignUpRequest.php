<?php

namespace App\Http\Request;

use JMS\Serializer\Annotation as Serializer;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordStrength;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints as Assert;

class SignUpRequest
{
    // ToDo: set password validator
    //@PasswordStrength(minLength=8, minStrength=3, message='Ваш пароль очень слабый')
    //vendor/rollerworks/password-strength-validator/src/Validator/Constraints/PasswordStrength.php

    /**
     * @var string
     * @Serializer\Type("string")
     * @Assert\Type("string")
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     * @Serializer\Type("string")
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    private $password;


    //private $recaptchaToken;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /*    public function getRecaptchaToken()
          {
              return $this->recaptchaToken;
          }*/
}
