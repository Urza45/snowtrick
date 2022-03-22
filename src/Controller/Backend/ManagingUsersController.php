<?php

namespace App\Controller\Backend;

use App\Form\BanUserType;
use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ManagingUsersController extends AbstractController
{
    /**
     * @Route("/admin/managing/users", name="app_managing_users")
     */
    public function index(UserRepository $repoUser, TrickRepository $repoTrick, CommentRepository $repoComment): Response
    {
        $users = $repoUser->findAll();

        return $this->render('managing_users/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/managing/users/{slug}", name="app_managing_user")
     */
    public function showUser()
    {
        //return $this->render('managing_users/index.html.twig', []);
        $reponse = new Response('Bienvenue dans Symfony');
        return $reponse;
    }

    /**
     * @Route("/admin/managing/banusers/{slug}", name="app_ban_user")
     */
    public function banUser(Request $request, UserRepository $repoUser)
    {
        $user = $repoUser->findOneBy(['slug' => $request->get('slug')]);

        $form = $this->createForm(BanUserType::class, $user);
        $form->handleRequest($request);

        return $this->render('managing_users/ban_user.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
        // $reponse = new Response('Bienvenue dans Symfony');
        // return $reponse;
    }
}
