<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\TrickType;
use App\Service\Captcha;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    private const NUMBER_TRICK_BY_ROW = 3;
    private const NUMBER_TRICK_BY_PAGE = 3;
    private const NUMBER_COMMENT_BY_PAGE = 5;

    /**
     * @Route("/", name="trick_home")
     */
    public function index(TrickRepository $repoTrick): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricksCount' => $repoTrick->count([]),
            'numberTrickByRow' => self::NUMBER_TRICK_BY_ROW,
            'numberTrickByPage' => self::NUMBER_TRICK_BY_PAGE,
            'tricks' => $repoTrick->findBy([], ['id' => 'ASC'], self::NUMBER_TRICK_BY_PAGE, 0)
        ]);
    }

    /**
     * @Route("/showmoretrick/{index}", name="show_more_trick", methods={"GET", "POST"},requirements={"index"="\d+"})
     */
    public function showMoreTrick(Request $request, TrickRepository $repoTrick)
    {
        return $this->render('trick/show_more_trick.html.twig', [
            'tricksCount' => $repoTrick->count([]),
            'numberTrickByRow' => self::NUMBER_TRICK_BY_ROW,
            'numberTrickByPage' => self::NUMBER_TRICK_BY_PAGE,
            'index' => $request->get('index'),
            'tricks' => $repoTrick->findBy([], ['id' => 'ASC'], self::NUMBER_TRICK_BY_PAGE, $request->get('index'))
        ]);
    }

    /**
     * @Route("/showmorecomment/{index}", name="show_more_comment", methods={"GET", "POST"},requirements={"index"="\d+"} )
     */
    public function showMoreComment(Request $request, TrickRepository $repoTrick, CommentRepository $repoComment)
    {
        dump($request);
        $trick = $repoTrick->findOneBy(['id' => $request->get('trickId')]);

        return $this->render('comment/index.html.twig', [
            'trick' => $trick,
            'listComment' => $repoComment->findBy(['trick' => $trick], ['id' => 'DESC'], self::NUMBER_COMMENT_BY_PAGE, $request->get('index')),
            'commentsCount' => $repoComment->count(['trick' => $trick]),
            'index' => $request->get('index'),
            'numberCommentByPage' => self::NUMBER_COMMENT_BY_PAGE,
        ]);
    }

    /**
     * @Route("/trick/{slug}", name="show_trick")
     */
    public function showTrick(
        TrickRepository $repoTrick,
        Request $request,
        Session $session,
        UserRepository $repoUser,
        CommentRepository $repoComment,
        ManagerRegistry $doctrine
    ) {
        $trick = $repoTrick->findOneBy(['slug' => $request->get('slug')]);
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $doctrine->getManager();
            $user = $repoUser->findOneByPseudo($this->getUser()->getUserIdentifier());

            // Captcha verification
            if (!($form->get('captcha')->getData() == $session->get('captcha'))) {
                $this->addFlash('comment', 'Le captcha saisi n\'est pas correct.');
                return $this->render('trick/show_trick.html.twig', [
                    'trick' => $trick,
                    'formComment' => $form->createView()
                ]);
            }
            $comment->setDisabled(false);
            $comment->setNew(true);
            $comment->setUser($user);
            $comment->setTrick($trick);

            $manager->persist($comment);
            $manager->flush();
            $this->addFlash('success', 'Votre commentaire a bien été enregistré.');
        }

        return $this->render('trick/show_trick.html.twig', [
            'trick' => $trick,
            'formComment' => $form->createView(),
            'listComment' => $repoComment->findBy(['trick' => $trick], ['id' => 'DESC'], self::NUMBER_COMMENT_BY_PAGE, 0),
            'commentsCount' => $repoComment->count(['trick' => $trick]),
            'numberCommentByPage' => self::NUMBER_COMMENT_BY_PAGE,
        ]);
    }

    /**
     * @Route("/modify_trick/{slug}", name="modify_trick")
     */
    public function modifyTrick(TrickRepository $repoTrick, Request $request, ManagerRegistry $doctrine)
    {
        $trick = $repoTrick->findOneBy(['slug' => $request->get('slug')]);

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $doctrine->getManager();

            $manager->persist($trick);
            $manager->flush();

            $this->addFlash('success', 'Vos modifications ont bien été enregistrées.');
        };

        return $this->render('trick/modify_trick.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick
        ]);
    }

    /**
     *  @Route("/captcha", name="captcha")
     */
    public function captcha(Captcha $captcha, Session $session)
    {
        return $captcha->captcha($session);
    }
}
