<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/api/user', name: 'app_api_user')]
    public function index(): JsonResponse
    {
        return $this->json($this->getUser(),200, [], ['groups' => 'main']);
    }
}
