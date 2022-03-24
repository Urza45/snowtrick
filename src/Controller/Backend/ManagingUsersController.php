<?php

namespace App\Controller\Backend;

use App\Form\BanUserType;
use App\Form\DeleteUserType;
use App\Form\ShowUserType;
use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    /**
     * @Route("/admin/managing/users/{slug}", name="app_managing_user")
     */
    public function showUser(Request $request, UserRepository $repoUser, ManagerRegistry $doctrine)
    {
        $user = $repoUser->findOneBy(['slug' => $request->get('slug')]);

        $form = $this->createForm(ShowUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $manager = $doctrine->getManager();
                //$manager->persist($user);
                $manager->flush();
                return new Response('<p class="text-success">Vos modifications ont bien été enregistrées.</p>');
            }
            dump($request);
            dump($form);
            return new Response($form->getErrors(true, true)[0]->getMessageTemplate());
        }

        return $this->render('managing_users/show_user.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'requete' => $request
        ]);
    }

    /**
     * @Route("/admin/managing/banusers/{slug}", name="app_managing_userban")
     */
    public function banUser(Request $request, UserRepository $repoUser, ManagerRegistry $doctrine)
    {
        $user = $repoUser->findOneBy(['slug' => $request->get('slug')]);

        $form = $this->createForm(BanUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $doctrine->getManager();
            //$manager->persist($user);
            $manager->flush();
            return new Response('<p class="text-success">Le status a bien été modifié.</p>');
        }

        return $this->render('managing_users/ban_user.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'requete' => $request
        ]);
    }

    /**
     * @Route("/admin/managing/deleteusers/{slug}", name="app_managing_userdelete")
     */
    public function deleteUser(
        Request $request,
        UserRepository $repoUser,
        CommentRepository $repoComment,
        ManagerRegistry $doctrine
    ) {
        $user = $repoUser->findOneBy(['slug' => $request->get('slug')]);

        $form = $this->createForm(DeleteUserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponse = $form->get('supprimer')->getData();
            if ($reponse) {
                if ($reponse == true) {
                    // Remove user'comments
                    $comments = $user->getComments();
                    foreach ($comments as $comment) {
                        $user->removeComment($comment);
                    }
                    // Remove user
                    $repoUser->remove($user);
                    return new Response('<p class="text-success">L\'utilisateur a bien été supprimé.</p>');
                }
                return new Response('<p class="text-success">L\'utilisateur n\'a pas été supprimé.</p>');
            }
            return new Response('<p class="text-success">L\'utilisateur n\'a pas été supprimé.</p>');
        }

        return $this->render('managing_users/delete_user.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
        // $reponse = new Response('Bienvenue dans Symfony');
        // return $reponse;
    }
}
