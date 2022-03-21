<?php

namespace App\Controller\Backend;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManagingUsersController extends AbstractController
{
    /**
     * @Route("/admin/managing/users", name="app_managing_users")
     */
    public function index(UserRepository $repoUser): Response
    {
        $users = $repoUser->findAll();

        return $this->render('managing_users/index.html.twig', [
            'users' => $users,
        ]);
    }
}
