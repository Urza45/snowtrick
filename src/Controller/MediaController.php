<?php

namespace App\Controller;

use App\Entity\Media;
use App\Form\VideoType;
use App\Service\FileUploader;
use App\Form\FileUploadTrickType;
use App\Repository\TrickRepository;
use App\Repository\TypeMediaRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MediaController extends AbstractController
{
    /**
     * @Route("/media", name="app_media")
     */
    public function index(): Response
    {
        return $this->render('media/index.html.twig', [
            'controller_name' => 'MediaController',
        ]);
    }

    /**
     * @Route("/modify_trick/{slug}/add_picture", name="add_picture_trick")
     */
    public function addPicture(
        TrickRepository $repoTrick,
        Request $request,
        ManagerRegistry $doctrine,
        FileUploader $fileUploader,
        TypeMediaRepository $repoTypeMedia
    ) {
        $trick = $repoTrick->findOneBy(['slug' => $request->get('slug')]);

        $media = new Media();

        $formMedia = $this->createForm(FileUploadTrickType::class);
        $formMedia->handleRequest($request);

        if ($formMedia->isSubmitted()) {
            if ($formMedia->isValid()) {

                $file = $formMedia['url']->getData();
                if ($file) {
                    $response = $fileUploader->upload($file, $request);
                    if ($response['status'] == 'fail') {
                        return new Response('<p class="text-danger">' . $response['message'] . '</p>');
                    }
                    $fileName = $response['message'];
                    $path = explode('.', $fileName);
                    $extension = end($path);
                    //$extension = pathinfo($file, PATHINFO_EXTENSION);
                    if ($fileName !== null) {
                        $manager = $doctrine->getManager();
                        $media->setLegend($formMedia['legend']->getData());
                        $media->setUrl('medias/tricks/' . $fileName);
                        $media->setFeaturePicture($formMedia['featurePicture']->getData());
                        $media->setTypeMedia($repoTypeMedia->findOneBy(['typeMedia' => $extension]));
                        $media->setTrick($trick);

                        $manager->persist($media);
                        $manager->flush();
                        return new Response('<p class="text-success">' . $extension . ' Le status a bien été modifié.</p>');
                    }
                    return new Response('<p class="text-danger">1</p>');
                }
                return new Response('<p class="text-danger">2</p>');
            }
            return new Response('<p class="text-danger">' . $formMedia->getErrors(true, true) . '</p>');
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

        $formMedia = $this->createForm(VideoType::class);
        $formMedia->handleRequest($request);

        return $this->render('service/video.html.twig', [
            'formMedia' => $formMedia->createView(),
            'trick' => $trick
        ]);
    }
}
