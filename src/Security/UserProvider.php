<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserProvider
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUser($email)
    {
        if (!$user = $this->userRepository->findOneBy(['email' => $email])) {
            throw new UserNotFoundException();
        }
        return $user;
    }
}