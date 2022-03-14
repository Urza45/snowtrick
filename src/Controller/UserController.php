<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AvatarType;
use App\Form\FileUploadType;
use App\Repository\UserRepository;
use App\Form\UserType;
use App\Repository\AvatarRepository;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Service\FileUploader;
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
        $manager = $doctrine->getManager();;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setStatusConnected(true);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('notice', 'Vos modification sont bien - 1 - enregitrées.');
        }

        return $this->render('user/index.html.twig', [
            'USER' => $user,
            'formUser' => $form->createView(),
            'tricks' => $tricks,
            'comments' => $comments
        ]);
    }

    /**
     * @Route("/profile/change_picture", name="change_picture_user")
     */
    public function changePicture(Request $request, FileUploader $fileUploader, UserRepository $repoUser, AvatarRepository $repoAvatar)
    {

        $form = $this->createForm(FileUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $file = $form['upload_file']->getData();
                if ($file) {
                    $fileName = $fileUploader->upload($file, $request);
                    if ($fileName !== null) {
                        $this->addFlash('notice', 'Vos modification sont bien enregitrées.');
                        $this->addFlash('notice', 'Vos modification sont bien enregitrées.' . $fileName);
                        return $this->redirectToRoute('app_user');
                    } else {
                        $this->addFlash('notice', 'Il y a eu un problème.' . $file);
                        return $this->redirectToRoute('app_user');
                    }
                }
            } else {
                $this->addFlash('notice', $form->getErrors(true)[0]->getMessageTemplate());
                return $this->redirectToRoute('app_user');
            }
        } else {
            dump($request->attributes->get('userId'));
            $user = $repoUser->findOneBy(['id' => $request->request->get('userId')]);
            $avatar = $repoAvatar->findOneBy(['id' => $user->getAvatar()]);
        }

        return $this->render('service/file_upload.html.twig', [
            'form' => $form->createView(),
            'request' => $request,
            'user' => $user,
            'avatar' => $avatar
        ]);
    }

    /**
     * @Route("/profile/change_password", name="change_password_user")
     */
    public function changePassword()
    {
    }
}
