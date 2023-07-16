<?php

namespace App\Security;

use App\Entity\ApiToken;
use App\Repository\ApiTokenRepository;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserApiProvider
{
    private $apiTokenRepository;

    public function __construct(ApiTokenRepository $apiTokenRepository)
    {
        $this->apiTokenRepository = $apiTokenRepository;
    }

    public function getUser($tokenValue)
    {
        if (!$token = $this->apiTokenRepository->findOneBy(['token' => $tokenValue])) {
            throw new TokenNotFoundException();
        }
        if ($token->getExpiresAt() < new \DateTime()) {
            throw new \Exception('Срок действия токена истек');
        }
        if (!$user = $token->getUser()) {
            throw new UserNotFoundException();
        }
        if (!$user->isActive()) {
            throw new \Exception('Пользователь неактвиен');
        }

        return $user;
    }
}