<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Avatar;
use App\Form\UserType;
use App\Form\ChangePasswordType;
use App\Form\FileUploadAvatarType;
use App\Repository\UserRepository;
use App\Services\FileUploader;
use App\Repository\AvatarRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
            $this->addFlash('success', 'Vos modifications sont bien enregistrées.');
        }

        return $this->render(
            'user/index.html.twig',
            [
                'user' => $user,
                'formUser' => $form->createView(),
                'tricks' => $tricks,
                'comments' => $comments
            ]
        );
    }

    /**
     * @Route("/profile/changePicture", name="change_picture_user")
     */
    public function changePicture(
        Request $request,
        FileUploader $fileUploader,
        UserRepository $repoUser,
        AvatarRepository $repoAvatar,
        ManagerRegistry $doctrine
    ) {
        $form = $this->createForm(FileUploadAvatarType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['upload_file']->getData();
            if ($file) {
                $fileName = $fileUploader->upload($file, 'avatar');
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                if ($fileName !== null) {
                    $manager = $doctrine->getManager();

                    $user = $repoUser->findOneByPseudo($this->getUser()->getUserIdentifier());

                    $avatar = null;

                    if ($user->getAvatar() !== null) {
                        $avatar = $user->getAvatar();
                    }

                    if (!$avatar) {
                        $avatar = new Avatar();
                        $avatar->addUser($user);
                        $avatar->setType($extension);
                        $manager->persist($avatar);
                    }
                    $avatar->setUrl('medias/avatars/' . $fileName['message']);

                    $manager->flush();

                    $this->addFlash('success', 'Vos modifications sont bien enregitrées.');
                    return $this->redirectToRoute('app_user');
                }
                $this->addFlash('notice', 'Il y a eu un problème :' . $file);
                return $this->redirectToRoute('app_user');
            }

            //$this->addFlash('notice', $form->getErrors(true)[0]->getMessageTemplate());
            //return $this->redirectToRoute('app_user');
        }

        $user = $repoUser->findOneBy(['id' => $request->request->get('userId')]);
        $avatar = $repoAvatar->findOneBy(['id' => $user->getAvatar()]);

        return $this->render(
            'service/file_upload_avatar.html.twig',
            [
                'form' => $form->createView(),
                'request' => $request,
                'user' => $user,
                'avatar' => $avatar
            ]
        );
    }

    /**
     * @Route("/profile/changePassword", name="change_password_user")
     */
    public function changePassword(
        Request $request,
        ManagerRegistry $doctrine,
        UserRepository $repoUser,
        UserPasswordHasherInterface $userPasswordHasher
    ) {
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $manager = $doctrine->getManager();
                $user = $repoUser->findOneByPseudo($this->getUser()->getUserIdentifier());

                if ($userPasswordHasher->isPasswordValid($this->getUser(), $form->get('oldPassword')->getData())) {
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $this->getUser(),
                            $form->get('newPassword')->getData()
                        )
                    );
                    $manager->persist($user);
                    $manager->flush();
                    $this->addFlash('success', 'Votre mot de passe bien été modifié.');
                    return $this->redirectToRoute('app_user');
                }
                $this->addFlash('notice', 'La saisie de votre mot de passe actuel est invalide.');
                return $this->redirectToRoute('app_user');
            }
            $this->addFlash('notice', $form->getErrors(true)[0]->getMessageTemplate());
            return $this->redirectToRoute('app_user');
        }

        return $this->render(
            'user/change_password.html.twig',
            [
                'formPassword' => $form->createView(),
            ]
        );
    }
}
