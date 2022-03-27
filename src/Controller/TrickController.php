<?php

namespace App\Controller;

use App\Service\Captcha;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/trick/{id}", name="show_trick")
     */
    public function showTrick(TrickRepository $repoTrick, Request $request)
    {
        $trick = $repoTrick->findOneBy(['id' => $request->get('id')]);

        return $this->render('trick/show_trick.html.twig', [
            'trick' => $trick
        ]);
    }

    /**
     * @Route("/modify_trick/{id}", name="modify_trick")
     */
    public function modifyTrick(TrickRepository $repoTrick, Request $request, ManagerRegistry $doctrine)
    {
        $trick = $repoTrick->findOneBy(['id' => $request->get('id')]);

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $doctrine->getManager();

            $manager->persist($trick);
            $manager->flush();

            $this->addFlash('success', 'Vos modification ont bien été enregistrées.');
        };

        return $this->render('trick/modify_trick.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick
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
