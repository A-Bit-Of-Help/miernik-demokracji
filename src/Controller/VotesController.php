<?php

namespace App\Controller;

use App\Entity\Votes;
use App\Form\VotesType;
use App\Repository\VotesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/votes")
 * @IsGranted("ROLE_USER")
 */
class VotesController extends AbstractController
{
    /**
     * @Route("/", name="votes_index", methods={"GET"})
     */
    public function index(VotesRepository $votesRepository): Response
    {
        return $this->render('votes/index.html.twig', [
            'votes' => $votesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="votes_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $vote = new Votes();
        $form = $this->createForm(VotesType::class, $vote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($vote);
            $entityManager->flush();

            return $this->redirectToRoute('votes_index');
        }

        return $this->render('votes/new.html.twig', [
            'vote' => $vote,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="votes_show", methods={"GET"})
     */
    public function show(Votes $vote): Response
    {
        return $this->render('votes/show.html.twig', [
            'vote' => $vote,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="votes_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Votes $vote): Response
    {
        $form = $this->createForm(VotesType::class, $vote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('votes_index');
        }

        return $this->render('votes/edit.html.twig', [
            'vote' => $vote,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="votes_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Votes $vote): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vote->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($vote);
            $entityManager->flush();
        }

        return $this->redirectToRoute('votes_index');
    }
}
