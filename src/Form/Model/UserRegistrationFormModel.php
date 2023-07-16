<?php

namespace App\Form\Model;

use App\Validator\UniqueUser;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserRegistrationFormModel
{
    #[NotBlank]
    #[Email]
    #[UniqueUser]
    public $email;

    public $firstName;

    #[NotBlank(message: 'Укажите пароль')]
    #[Length(min: 6, minMessage: 'Длинна пароля не может быть меньше 6-ти символов')]
    public $password;

    #[IsTrue(message: 'Вы должны согласиться с условиями, чтобы продлжить')]
    public $agreeTerms;

}