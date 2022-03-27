<?php

namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManagingTricksController extends AbstractController
{
    /**
     * @Route("/admin/managing/tricks", name="app_managing_tricks")
     */
    public function index(): Response
    {
        return $this->render('managing_tricks/index.html.twig', [
            'controller_name' => 'ManagingTricksController',
        ]);
    }
}
