<?php

namespace App\Controller;

use App\Entity\VotesResult;
use App\Form\VotesResultType;
use App\Repository\VotesResultRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/votes_result")
 */
class VotesResultController extends AbstractController
{
    /**
     * @Route("/", name="votes_result_index", methods={"GET"})
     */
    public function index(VotesResultRepository $votesResultRepository): Response
    {
        return $this->render('votes_result/index.html.twig', [
            'votes_results' => $votesResultRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="votes_result_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $votesResult = new VotesResult();
        $form = $this->createForm(VotesResultType::class, $votesResult);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($votesResult);
            $entityManager->flush();

            return $this->redirectToRoute('votes_result_index');
        }

        return $this->render('votes_result/new.html.twig', [
            'votes_result' => $votesResult,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="votes_result_show", methods={"GET"})
     */
    public function show(VotesResult $votesResult): Response
    {
        return $this->render('votes_result/show.html.twig', [
            'votes_result' => $votesResult,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="votes_result_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, VotesResult $votesResult): Response
    {
        $form = $this->createForm(VotesResultType::class, $votesResult);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('votes_result_index');
        }

        return $this->render('votes_result/edit.html.twig', [
            'votes_result' => $votesResult,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="votes_result_delete", methods={"POST"})
     */
    public function delete(Request $request, VotesResult $votesResult): Response
    {
        if ($this->isCsrfTokenValid('delete'.$votesResult->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($votesResult);
            $entityManager->flush();
        }

        return $this->redirectToRoute('votes_result_index');
    }
}
