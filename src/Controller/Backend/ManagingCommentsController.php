<?php

namespace App\Controller\Backend;

use App\Form\BanType;
use App\Form\DeleteType;
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

        return $this->render(
            'managing_comments/index.html.twig',
            [
                'comments' => $comments,
            ]
        );
    }

    /**
     * @Route("/admin/comment/{id}", name="app_managing_comment")
     */
    public function modifyComment(Request $request, CommentRepository $repoComment, ManagerRegistry $doctrine)
    {
        $comment = $repoComment->findOneBy(['id' => $request->get('id')]);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $manager = $doctrine->getManager();
                $manager->persist($comment);
                $manager->flush();
                return new Response('<p class="text-success">Vos modifications ont bien été enregistrées.</p>');
            }
            return new Response($form->getErrors(true, true)[0]->getMessageTemplate());
        }


        return $this->render(
            'managing_comments/show_comment.html.twig',
            [
                'form' => $form->createView(),
                'requete' => $request,
                'comment' => $comment
            ]
        );
    }

    /**
     * @Route("/admin/comment/{id}/ban", name="app_managing_ban_comment")
     */
    public function banComment(Request $request, CommentRepository $repoComment, ManagerRegistry $doctrine)
    {
        $comment = $repoComment->findOneBy(['id' => $request->get('id')]);

        $form = $this->createForm(BanType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $manager = $doctrine->getManager();
                $manager->persist($comment);
                $manager->flush();
                return new Response('<p class="text-success">Vos modifications ont bien été enregistrées.</p>');
            }
            return new Response($form->getErrors(true, true)[0]->getMessageTemplate());
        }

        return $this->render(
            'managing_comments/ban_comment.html.twig',
            [
                'form' => $form->createView(),
                'requete' => $request,
                'comment' => $comment
            ]
        );
    }

    /**
     * @Route("/admin/comment/{id}/delete", name="app_managing_delete_comment")
     */
    public function deleteComment(Request $request, CommentRepository $repoComment)
    {
        $comment = $repoComment->findOneBy(['id' => $request->get('id')]);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponse = $form->get('supprimer')->getData();
            if ($reponse == true) {
                // Suppression du commentaire
                $repoComment->remove($comment, true);
                return new Response(
                    '<p class="text-success">Le commentaire a bien été supprimé.</p>'
                );
            }
        }

        return $this->render(
            'managing_comments/delete_comment.html.twig',
            [
                'form' => $form->createView(),
                'requete' => $request,
                'comment' => $comment
            ]
        );
    }
}
