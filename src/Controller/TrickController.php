<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Form\VideoType;
use App\Form\DeleteType;
use App\Services\Captcha;
use App\Entity\TypeMedia;
use App\Services\FileUploader;
use App\Form\FileUploadTrickType;
use App\Repository\UserRepository;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use App\Repository\TypeMediaRepository;
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

    /**
     * @Route("/", name="trick_home")
     */
    public function index(TrickRepository $repoTrick): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricksCount' => $repoTrick->count([]),
            'numberTrickByRow' => self::NUMBER_TRICK_BY_ROW,
            'numberTrickByPage' => self::NUMBER_TRICK_BY_PAGE,
            'tricks' => $repoTrick->findBy([], ['id' => 'DESC'], self::NUMBER_TRICK_BY_PAGE, 0)
        ]);
    }

    /**
     * @Route("/showmoretrick/{index}", name="show_more_trick", methods={"GET", "POST"},requirements={"index"="\d+"})
     */
    public function showMoreTrick(Request $request, TrickRepository $repoTrick)
    {
        $tricks = $repoTrick->findBy([], ['id' => 'DESC'], self::NUMBER_TRICK_BY_PAGE, $request->get('index'));

        return $this->render('trick/show_more_trick.html.twig', [
            'tricksCount' => $repoTrick->count([]),
            'numberTrickByRow' => self::NUMBER_TRICK_BY_ROW,
            'numberTrickByPage' => self::NUMBER_TRICK_BY_PAGE,
            'index' => $request->get('index'),
            'tricks' => $repoTrick->findBy([], ['id' => 'DESC'], self::NUMBER_TRICK_BY_PAGE, $request->get('index'))
        ]);
    }

    /**
     * @Route("/trick/{slug}", name="show_trick")
     */
    public function showTrick(TrickRepository $repoTrick, Request $request, MediaRepository $repoMedia)
    {
        $trick = $repoTrick->findOneBy(['slug' => $request->get('slug')]);

        $pictures = $repoMedia->getImage($trick->getId());
        $videos = $repoMedia->getVideo($trick->getId());

        return $this->render('trick/show_trick.html.twig', [
            'trick' => $trick,
            'pictures' => $pictures,
            'videos' => $videos
        ]);
    }

    /**
     * @Route("/add_trick", name="add_trick")
     */
    public function addTrick(Request $request, UserRepository $repoUser, ManagerRegistry $doctrine)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $repoUser->findOneByPseudo($this->getUser()->getUserIdentifier());
            $trick->setUser($user);

            $manager = $doctrine->getManager();

            $manager->persist($trick);
            $manager->flush();

            $this->addFlash('success', 'Votre article a bien été enregistré.');
            return $this->redirectToRoute('modify_trick', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/modify_trick.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick
        ]);
    }

    /**
     * @Route("/modify_trick/{slug}", name="modify_trick")
     */
    public function modifyTrick(TrickRepository $repoTrick, Request $request, ManagerRegistry $doctrine, MediaRepository $repoMedia)
    {
        $trick = $repoTrick->findOneBy(['slug' => $request->get('slug')]);

        $pictures = $repoMedia->getImage($trick->getId());
        $videos = $repoMedia->getVideo($trick->getId());

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
            'trick' => $trick,
            'pictures' => $pictures,
            'videos' => $videos
        ]);
    }

    /**
     * @Route("/delete_trick/{slug}", name="delete_trick")
     */
    public function deleteTrick(TrickRepository $repoTrick, Request $request, ManagerRegistry $doctrine)
    {
        $trick = $repoTrick->findOneBy(['slug' => $request->get('slug')]);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponse = $form->get('supprimer')->getData();
            if ($reponse) {
                if ($reponse == true) {
                    return new Response('<p class="text-success">Vous avez choisi oui.</p>');
                }
                return new Response('<p class="text-success">L\'utilisateur n\'a pas été supprimé.</p>');
            }
            return new Response('<p class="text-success">L\'utilisateur n\'a pas été supprimé.</p>');
        }

        return $this->render('trick/delete_trick.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick
        ]);
    }


    /**
     * @Route("/captcha", name="captcha")
     */
    public function captcha(Captcha $captcha, Session $session)
    {
        return $captcha->captcha($session);
    }
}
