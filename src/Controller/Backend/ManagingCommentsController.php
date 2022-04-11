<?php

namespace App\Controller\Backend;

use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ManagingCommentsController extends AbstractController
{
    /**
     * @Route("/admin/comments", name="app_managing_comments")
     */
    public function index(CommentRepository $repoComment): Response
    {
        $comments = $repoComment->findAll();

        return $this->render('managing_comments/index.html.twig', [
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/admin/comment/{id}", name="app_managing_comment")
     */
    public function modifyComment(Request $request, CommentRepository $repoComment, ManagerRegistry $doctrine)
    {
        $comment = $repoComment->findOneBy(['id' => $request->get('id')]);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        return $this->render('managing_comments/show_comment.html.twig', [
            'form' => $form->createView(),
            'requete' => $request
        ]);
    }

    /**
     * @Route("/admin/comment/{id}/ban", name="app_managing_ban_comment")
     */
    public function banComment(Request $request, CommentRepository $repoComment, ManagerRegistry $doctrine)
    {
        return new Response('Hello');
    }

    /**
     * @Route("/admin/comment/{id}/delete", name="app_managing_delete_comment")
     */
    public function deleteComment(Request $request, CommentRepository $repoComment, ManagerRegistry $doctrine)
    {
        return new Response('Hello');
    }
}
