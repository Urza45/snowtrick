<?php

/**
 * 
 */

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Form\DeleteType;
use App\Form\CommentType;
use App\Services\Captcha;
use App\Services\FileUploader;
use App\Repository\UserRepository;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\Constraint\ExceptionCode;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\CssSelector\Exception\ExpressionErrorException;

/**
 * TrickController
 */
class TrickController extends AbstractController
{
    private const NUMBER_TRICK_BY_ROW = 5;
    private const NUMBER_TRICK_BY_PAGE = 15;
    private const NUMBER_COMMENT_BY_PAGE = 5;

    /**
     * Index
     * 
     * @param TrickRepository $repoTrick
     * 
     * @Route("/", name="trick_home")
     * 
     * @return Response
     */
    public function index(TrickRepository $repoTrick): Response
    {
        return $this->render(
            'trick/index.html.twig',
            [
                'tricksCount' => $repoTrick->count([]),
                'numberTrickByRow' => self::NUMBER_TRICK_BY_ROW,
                'numberTrickByPage' => self::NUMBER_TRICK_BY_PAGE,
                'tricks' => $repoTrick->findBy(
                    [],
                    ['id' => 'DESC'],
                    self::NUMBER_TRICK_BY_PAGE,
                    0
                )
            ]
        );
    }

    /**
     * ShowMoreTrick
     *
     * @param mixed $request
     * @param mixed $repoTrick
     * 
     * @Route("/showmoretrick/{index}", name="show_more_trick", methods={"GET", "POST"},requirements={"index"="\d+"})
     * 
     * @return void
     */
    public function showMoreTrick(Request $request, TrickRepository $repoTrick)
    {
        return $this->render(
            'trick/show_more_trick.html.twig',
            [
                'tricksCount' => $repoTrick->count([]),
                'numberTrickByRow' => self::NUMBER_TRICK_BY_ROW,
                'numberTrickByPage' => self::NUMBER_TRICK_BY_PAGE,
                'index' => $request->get('index'),
                'tricks' => $repoTrick->findBy(
                    [],
                    ['id' => 'DESC'],
                    self::NUMBER_TRICK_BY_PAGE,
                    $request->get('index')
                )
            ]
        );
    }

    /**
     * ShowMoreComment
     *
     * @param mixed $request
     * @param mixed $repoTrick
     * @param mixed $repoComment
     * 
     * @Route("/morecomment/{index}", name="show_more_comment", methods={"GET", "POST"}, requirements={"index"="\d+"} )
     * 
     * @return void
     */
    public function showMoreComment(
        Request $request,
        TrickRepository $repoTrick,
        CommentRepository $repoComment
    ) {
        $trick = $repoTrick->findOneBy(['id' => $request->get('trickId')]);

        return $this->render(
            'comment/index.html.twig',
            [
                'trick' => $trick,
                'listComment' => $repoComment->findBy(
                    ['trick' => $trick],
                    ['id' => 'DESC'],
                    self::NUMBER_COMMENT_BY_PAGE,
                    $request->get('index')
                ),
                'commentsCount' => $repoComment->count(['trick' => $trick]),
                'index' => $request->get('index'),
                'numberCommentByPage' => self::NUMBER_COMMENT_BY_PAGE,
            ]
        );
    }

    /**
     * addTrick
     * 
     * @Route("/manageTrick/add", name="add_trick")
     *
     * @return void
     */
    public function addTrick(
        Request $request,
        UserRepository $repoUser,
        ManagerRegistry $doctrine,
        TrickRepository $repoTrick
    ) {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $repoUser->findOneByPseudo(
                $this->getUser()->getUserIdentifier()
            );
            $trick->setUser($user);

            $manager = $doctrine->getManager();

            $manager->persist($trick);

            if ($repoTrick->findOneBySlug($trick->getSlug()) !== null) {
                $this->addFlash('notice', 'Une figure possède déjà ce titre.');
                return $this->render(
                    'trick/modify_trick.html.twig',
                    [
                        'form' => $form->createView(),
                        'trick' => $trick,
                        'add' => 1
                    ]
                );
            }

            $manager->flush();

            $this->addFlash('success', 'Votre figure a bien été enregistrée.');
            return $this->redirectToRoute(
                'modify_trick',
                [
                    'slug' => $trick->getSlug(),
                ]
            );
        }

        return $this->render(
            'trick/modify_trick.html.twig',
            [
                'form' => $form->createView(),
                'trick' => $trick,
                'add' => 1
            ]
        );
    }

    /**
     * ShowTrick
     * 
     * @Route("/trick/{slug}", name="show_trick")
     *
     * @return void
     */
    public function showTrick(
        TrickRepository $repoTrick,
        Request $request,
        Session $session,
        UserRepository $repoUser,
        CommentRepository $repoComment,
        ManagerRegistry $doctrine,
        MediaRepository $repoMedia
    ) {
        $trick = $repoTrick->findOneBy(['slug' => $request->get('slug')]);
        $comment = new Comment();

        if ($trick) {
            $pictures = $repoMedia->getImage($trick->getId());
            $videos = $repoMedia->getVideo($trick->getId());

            $form = $this->createForm(CommentType::class, $comment);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $manager = $doctrine->getManager();
                $user = $repoUser->findOneByPseudo(
                    $this->getUser()->getUserIdentifier()
                );

                // Captcha verification
                if (!($form->get('captcha')->getData() == $session->get('captcha'))) {
                    $this->addFlash(
                        'comment',
                        'Le captcha saisi n\'est pas correct.'
                    );
                    return $this->render(
                        'trick/show_trick.html.twig',
                        [
                            'trick' => $trick,
                            'formComment' => $form->createView(),
                            'listComment' => $repoComment->findBy(
                                ['trick' => $trick],
                                ['id' => 'DESC'],
                                self::NUMBER_COMMENT_BY_PAGE,
                                0
                            ),
                            'commentsCount' => $repoComment->count(
                                [
                                    'trick' => $trick
                                ]
                            ),
                            'numberCommentByPage' => self::NUMBER_COMMENT_BY_PAGE,
                            'pictures' => $pictures,
                            'videos' => $videos
                        ]
                    );
                }
                $comment->setDisabled(false);
                $comment->setNew(true);
                $comment->setUser($user);
                $comment->setTrick($trick);

                $manager->persist($comment);
                $manager->flush();
                $this->addFlash(
                    'success',
                    'Votre commentaire a bien été enregistré.'
                );
            }

            return $this->render(
                'trick/show_trick.html.twig',
                [
                    'trick' => $trick,
                    'formComment' => $form->createView(),
                    'listComment' => $repoComment->findBy(
                        ['trick' => $trick],
                        ['id' => 'DESC'],
                        self::NUMBER_COMMENT_BY_PAGE,
                        0
                    ),
                    'commentsCount' => $repoComment->count(['trick' => $trick]),
                    'numberCommentByPage' => self::NUMBER_COMMENT_BY_PAGE,
                    'pictures' => $pictures,
                    'videos' => $videos
                ]
            );
        }

        return $this->redirectToRoute('trick_home');
    }

    /**
     * ModifyTrick
     * 
     * @Route("/manageTrick/modify/{slug}", name="modify_trick")
     *
     * @return void
     */
    public function modifyTrick(
        TrickRepository $repoTrick,
        Request $request,
        ManagerRegistry $doctrine,
        MediaRepository $repoMedia,
        SluggerInterface $slugger
    ) {
        $trick = $repoTrick->findOneBy(['slug' => $request->get('slug')]);

        $pictures = $repoMedia->getImage($trick->getId());
        $videos = $repoMedia->getVideo($trick->getId());

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $doctrine->getManager();

            $trick->setSlug(
                (string) $slugger->slug((string) $trick->getTitle())->lower()
            );
            $manager->persist($trick);

            $tricks = $repoTrick->findBySlug($trick->getSlug());

            $verif = false;
            foreach ($tricks as $sample) {
                if ($sample->getId() !== $trick->getId()) {
                    $verif = true;
                }
            }

            if ($verif) {
                $this->addFlash('notice', 'Une figure possède déjà ce titre.');
                return $this->render(
                    'trick/modify_trick.html.twig',
                    [
                        'form' => $form->createView(),
                        'trick' => $trick,
                        'pictures' => $pictures,
                        'videos' => $videos
                    ]
                );
            }

            $manager->persist($trick);
            $manager->flush();

            $this->addFlash(
                'success',
                'Vos modifications ont bien été enregistrées.'
            );
        };

        return $this->render(
            'trick/modify_trick.html.twig',
            [
                'form' => $form->createView(),
                'trick' => $trick,
                'pictures' => $pictures,
                'videos' => $videos
            ]
        );
    }

    /**
     * DeleteTrick
     * Delete a trick and all comments and media attached to it
     * 
     * @param TrickRepository   $repoTrick    
     * @param Request           $request
     * @param FileUploader      $fileUploader 
     * @param MediaRepository   $repoMedia   
     * @param CommentRepository $repoComment
     * 
     * @Route("/manageTrick/delete/{slug}", name="delete_trick")
     * 
     * @return void
     */
    public function deleteTrick(
        TrickRepository $repoTrick,
        Request $request,
        FileUploader $fileUploader,
        MediaRepository $repoMedia,
        CommentRepository $repoComment
    ) {
        $trick = $repoTrick->findOneBy(['slug' => $request->get('slug')]);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponse = $form->get('supprimer')->getData();
            if ($reponse == true) {
                // Suppression des commentaires
                $comments = $trick->getComments();
                foreach ($comments as $comment) {
                    $repoComment->remove($comment, true);
                }
                // Suppression des medias
                $medias = $trick->getMedia();
                foreach ($medias as $media) {
                    $fileUploader->deleteFile(
                        $media,
                        $media->getTypeMedia(),
                        $repoMedia
                    );
                }
                // Suppression du trick
                $repoTrick->remove($trick, true);
                return new Response(
                    '<p class="text-success">Le trick a bien été supprimé.</p>'
                );
            }
            return new Response(
                '<p class="text-success">Le trick n\'a pas été supprimé.</p>'
            );
        }

        return $this->render(
            'trick/delete_trick.html.twig',
            [
                'form' => $form->createView(),
                'trick' => $trick
            ]
        );
    }

    /**
     * Captcha
     *
     * @param Captcha $captcha Generate a picture based 
     * @param Session $session Saves a random number
     * 
     * @Route("/captcha", name="captcha")
     * 
     * @return void
     */
    public function captcha(Captcha $captcha, Session $session)
    {
        return $captcha->captcha($session);
    }
}
