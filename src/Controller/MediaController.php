<?php

namespace App\Controller;

use App\Entity\Media;
use App\Form\VideoType;
use App\Form\DeleteType;
use App\Form\ModifyMediaType;
use App\Services\FileUploader;
use App\Services\YouTubeVideo;
use App\Form\FileUploadTrickType;
use App\Repository\MediaRepository;
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
     * @Route("/media_show", name="app_media")
     */
    public function showMedia(
        Request $request,
        MediaRepository $repoMedia,
        TypeMediaRepository $repoTypeMedia
    ): Response {
        $media = $repoMedia->findOneBy(['id' => $request->get('userId')]);
        $typeMedia = $repoTypeMedia->findOneBy(['id' => $media->getTypeMedia()]);

        return $this->render('media/show.html.twig', [
            'controller_name' => 'MediaController',
            'request' => $request,
            'media' => $media,
            'typeMedia' => $typeMedia
        ]);
    }

    /**
     * @Route("/media_modify", name="app_modif_media")
     */
    public function modifyMedia(
        Request $request,
        MediaRepository $repoMedia,
        TypeMediaRepository $repoTypeMedia,
        ManagerRegistry $doctrine
    ) {
        $media = $repoMedia->findOneBy(['id' => $request->get('userId')]);
        $typeMedia = $repoTypeMedia->findOneBy(['id' => $media->getTypeMedia()]);

        $form = $this->createForm(ModifyMediaType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $manager = $doctrine->getManager();
                $manager->persist($media);
                $manager->flush();

                $repoMedia->updateFeaturePicture($media);

                return new Response('<p class="text-success">Vos modifications sont bien enregistrées.</p>');
            }
            return new Response('<p class="text-danger">' . $form->getErrors(true, true) . '</p>');
        }

        return $this->render('media/modify.html.twig', [
            'form' => $form->createView(),
            'request' => $request,
            'media' => $media,
            'typeMedia' => $typeMedia
        ]);
    }

    /**
     * @Route("/media_delete", name="app_delete_media")
     */
    public function deleteMedia(
        Request $request,
        MediaRepository $repoMedia,
        TypeMediaRepository $repoTypeMedia
    ) {
        $media = $repoMedia->findOneBy(['id' => $request->get('userId')]);
        $typeMedia = $repoTypeMedia->findOneBy(['id' => $media->getTypeMedia()]);

        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponse = $form->get('supprimer')->getData();

            if ($reponse) {
                if ($reponse == true) {
                    $message = '';
                    if ($typeMedia->getGroupMedia() == 'Image') {
                        // Suppression de la miniature
                        if (!unlink($media->getThumbUrl())) {
                            $message .= 'La miniature n\'a pas pas pu être supprimée';
                        };
                        // Suppression de l'image
                        if (!unlink($media->getUrl())) {
                            if ($message <> '') {
                                $message .= '<br/>';
                            }
                            $message .= 'La photographie n\'a pas pas pu être supprimée';
                        };
                    }
                    if ($message <> '') {
                        $message .= 'L\'entrée en base de données est conservée.';
                        $message = '<p class="text-danger">' . $message . '</p>';
                        return new Response($message);
                    } else {
                        // Suppression de l'entrée en base de données.
                        $repoMedia->remove($media, true);
                        return new Response('<p class="text-success">Le média a bien été supprimé.</p>');
                    }
                }
            }

            return new Response('<p class="text-success">Pas de suppression</p>');
        }

        return $this->render('media/delete.html.twig', [
            'form' => $form->createView(),
            'request' => $request,
            'media' => $media,
            'typeMedia' => $typeMedia
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
                        $media->setThumbUrl('medias/tricks/' . 'thumbs_' . $fileName);
                        $media->setFeaturePicture($formMedia['featurePicture']->getData());
                        $media->setTypeMedia($repoTypeMedia->findOneBy(['typeMedia' => $extension]));
                        $media->setTrick($trick);

                        $manager->persist($media);
                        $manager->flush();
                        return new Response('<p class="text-success">La photographie a bien été enregistrée.</p>');
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
    public function addVideo(
        TrickRepository $repoTrick,
        Request $request,
        ManagerRegistry $doctrine,
        YouTubeVideo $youTubeVideo,
        TypeMediaRepository $repoTypeMedia
    ) {
        $trick = $repoTrick->findOneBy(['slug' => $request->get('slug')]);

        $media = new Media();

        $formMedia = $this->createForm(VideoType::class);
        $formMedia->handleRequest($request);

        if ($formMedia->isSubmitted() && $formMedia->isValid()) {

            $newUrl = $youTubeVideo->videoIframeYT($formMedia->get('url')->getData());
            $newImage = $youTubeVideo->videoImgYT($formMedia->get('url')->getData());

            $manager = $doctrine->getManager();
            $media->setLegend($formMedia['legend']->getData());
            $media->setUrl('' . $newUrl);
            $media->setThumbUrl('' . $newImage);
            $media->setFeaturePicture(false);
            $media->setTypeMedia($repoTypeMedia->findOneBy(['typeMedia' => 'mp4']));
            $media->setTrick($trick);

            $manager->persist($media);
            $manager->flush();



            return new Response('Nouvelle URL : ' . $newUrl . '<br/>' . $newImage);
        }

        return $this->render('service/video.html.twig', [
            'formMedia' => $formMedia->createView(),
            'trick' => $trick
        ]);
    }
}
