<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Service\Captcha;
use App\Form\FileUploadTrickType;
use App\Repository\TrickRepository;
use App\Service\FileUploaderAvatar;
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
     * @Route("/trick/{slug}", name="show_trick")
     */
    public function showTrick(TrickRepository $repoTrick, Request $request)
    {
        $trick = $repoTrick->findOneBy(['slug' => $request->get('slug')]);

        return $this->render('trick/show_trick.html.twig', [
            'trick' => $trick
        ]);
    }

    /**
     * @Route("/add_trick", name="add_trick")
     */
    public function addTrick(Request $request)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        return $this->render('trick/modify_trick.html.twig', [
            'trick' => $trick
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
     * @Route("/captcha", name="captcha")
     */
    public function captcha(Captcha $captcha, Session $session)
    {
        return $captcha->captcha($session);
    }

    /**
     * @Route("/modify_trick/{slug}/add_picture", name="add_picture_trick")
     */
    public function addPicture(
        TrickRepository $repoTrick,
        Request $request,
        ManagerRegistry $doctrine,
        FileUploaderAvatar $fileUploader
    ) {
        $trick = $repoTrick->findOneBy(['slug' => $request->get('slug')]);

        $media = new Media();

        $formMedia = $this->createForm(FileUploadTrickType::class);
        $formMedia->handleRequest($request);

        if ($formMedia->isSubmitted()) {
            if ($formMedia->isValid()) {

                $file = $formMedia['url']->getData();
                if ($file) {
                    $fileName = $fileUploader->upload($file, $request);
                    $extension = pathinfo($file, PATHINFO_EXTENSION);
                    if ($fileName !== null) {
                        $manager = $doctrine->getManager();
                        $media->setLegend($formMedia['legend']->getData());
                        $media->setUrl('medias/tricks/' . $fileName);
                        $media->setFeaturePicture($formMedia['featurePicture']->getData());
                        $media->setTypeMedia($formMedia['typeMedia']->getData());
                        $media->setTrick($trick);

                        $manager->persist($media);
                        $manager->flush();
                        return new Response('<p class="text-success">Le status a bien été modifié.</p>');
                    }
                    return new Response('<p class="text-danger">1</p>');
                }
                return new Response('<p class="text-danger">2</p>');
            }
            return new Response('<p class="text-danger">' . $formMedia['url']->getData() . '</p>');
        }
        return $this->render('service/picture.html.twig', [
            'formMedia' => $formMedia->createView(),
            'trick' => $trick
        ]);
    }

    /**
     * @Route("/modify_trick/{slug}/add_video", name="add_video_trick")
     */
    public function addVideo(TrickRepository $repoTrick, Request $request, ManagerRegistry $doctrine)
    {
        $trick = $repoTrick->findOneBy(['slug' => $request->get('slug')]);

        return $this->render('service/video.html.twig', []);
    }
}
