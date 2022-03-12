<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\UserType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="app_user")
     */
    public function index(
        UserRepository $repoUser,
        Request $request,
        ManagerRegistry $doctrine
    ): Response {
        $user = new User();
        $user = $repoUser->findOneByPseudo($this->getUser()->getUserIdentifier());
        $tricks = $user->getTricks();
        $comments = $user->getComments();

        $manager = $doctrine->getManager();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setStatusConnected(true);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('notice', 'Vos modification sont bien enregitrées.');
        }

        return $this->render('user/index.html.twig', [
            'USER' => $user,
            'formUser' => $form->createView(),
            'tricks' => $tricks,
            'comments' => $comments
        ]);
    }
}
