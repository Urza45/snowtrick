<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\MailerService;
use App\Form\ResetPasswordType;
use App\Form\ForgotPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ForgotPasswordController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private SessionInterface $session;

    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        SessionInterface $session,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/forgot/password", name="app_forgot_password", methods={"GET", "POST"})
     */
    public function sendRecoveryLink(
        Request $request,
        MailerService $sendEmail,
        TokenGeneratorInterface $tokenGenerator,
        ManagerRegistry $doctrine
    ): Response {
        $form = $this->createForm(ForgotPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $doctrine->getManager();

            $user = $this->userRepository->findOneByPseudo($form['pseudo']->getData());

            /* We make a lure ! */
            if (!$user) {
                $this->addFlash('success', 'Un email vous a été envoyé pour réinitailiser votre mot de pase.');
                $this->redirectToRoute('app_login');
            }

            $user->setValidationKey($tokenGenerator->generateToken());

            $manager->persist($user);
            $manager->flush();

            $emailParameters = [
                'subject' => 'Réinitialisation de votre mot de passe',
                'from' => '',
                'to' => $user->getEmail(),
                'template' => 'forgot_password/send_email.html.twig',
                'parameters' => [
                    'user' => $user
                ]
            ];

            // Send Mail here
            $sendEmail->send($emailParameters);

            $this->addFlash('success', 'Un email vous a été envoyé pour réinitailiser votre mot de pase.');
            $this->redirectToRoute('app_login');
        }

        return $this->render('forgot_password/index.html.twig', [
            'forgotPasswordForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/forgot-password/{id<\d+>}/{token}", name="app_retrieve_credentials", methods={"GET"})
     */
    public function retrieveCredentialFromUrl(string $token, User $user): RedirectResponse
    {
        $this->session->set('Reset-Password-Token-Url', $token);
        $this->session->set('Reset-Password-User-Pseudo', $user->getPseudo());

        return $this->redirectToRoute('app_reset_password');
    }

    /**
     * @Route("/reset-password", name="app_reset_password", methods={"GET", "POST"})
     */
    public function resetPassword(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        ManagerRegistry $doctrine
    ) {
        [
            'token' => $token,
            'pseudo' => $pseudo
        ] = $this->getCredentialsFromSession();

        $user = $this->userRepository->findOneByPseudo($pseudo);
        if (!$user) {
            return $this->redirectToRoute('app_forgot_password');
        }

        if (($user->getValidationKey() === null) || ($user->getValidationKey() !== $token)) {
            return $this->redirectToRoute('app_forgot_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $doctrine->getManager();
            // encode the password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            // Clear the token to make it unusabled
            $user->setValidationKey('');

            $manager->flush();

            $this->removeCredentialFromSession();

            $this->addFlash('success', 'Votre mot de passe a bien été modifié, vous pouvez à présent vous connecter');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('forgot_password/reset_password_html.twig', [
            'forgotPasswordForm' => $form->createView()
        ]);
    }

    private function getCredentialsFromSession(): array
    {
        return [
            'token' => $this->session->get('Reset-Password-Token-Url'),
            'pseudo' => $this->session->get('Reset-Password-User-Pseudo')
        ];
    }

    private function removeCredentialFromSession()
    {
        $this->session->remove('Reset-Password-Token-Url');
        $this->session->remove('Reset-Password-User-Pseudo');
    }
}
