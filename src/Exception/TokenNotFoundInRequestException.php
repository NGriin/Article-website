<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class TokenNotFoundInRequestException extends AuthenticationException
{
    public function getMessageKey()
    {
        return 'Необходимо указать токен';
    }

}