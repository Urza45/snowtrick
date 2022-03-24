<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use App\Service\Captcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @Route("/", name="trick_home")
     */
    public function index(TrickRepository $repoTrick): Response
    {
        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
            'tricks' => $repoTrick->findAll()
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
