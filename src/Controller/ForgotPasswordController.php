<?php

namespace App\Controller;

use App\Service\MailerService;
use App\Form\ForgotPasswordType;
use App\Repository\UserRepository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

            // Send Mail here
            $sendEmail->send(
                'Réinitialisation de votre mot de passe',
                '',
                $user->getEmail(),
                'forgot_password/send_email.html.twig',
                []
            );

            $this->addFlash('success', 'Un email vous a été envoyé pour réinitailiser votre mot de pase.');
            $this->redirectToRoute('app_login');
        }

        return $this->render('forgot_password/index.html.twig', [
            'forgotPasswordForm' => $form->createView(),
        ]);
    }
}
