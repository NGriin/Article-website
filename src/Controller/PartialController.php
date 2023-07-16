<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PartialController extends AbstractController
{
    public function lastComments(CommentRepository $commentRepository): Response
    {

        $comments = $commentRepository->findBy(array(), ['createdAt' => 'desc'], 3);

        return $this->render('partical/last_comments.html.twig', [
            'comments' => $comments,
        ]);
    }
}
