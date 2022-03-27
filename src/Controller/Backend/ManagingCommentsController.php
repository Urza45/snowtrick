<?php

namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManagingCommentsController extends AbstractController
{
    /**
     * @Route("/admin/managing/comments", name="app_managing_comments")
     */
    public function index(): Response
    {
        return $this->render('managing_comments/index.html.twig', [
            'controller_name' => 'ManagingCommentsController',
        ]);
    }
}
