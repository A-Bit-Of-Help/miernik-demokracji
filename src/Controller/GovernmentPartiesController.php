<?php

namespace App\Controller;

use App\Entity\GovernmentParties;
use App\Form\GovernmentPartiesType;
use App\Repository\GovernmentPartiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/government_parties")
 */
class GovernmentPartiesController extends AbstractController
{
    /**
     * @Route("/", name="government_parties_index", methods={"GET"})
     */
    public function index(GovernmentPartiesRepository $governmentPartiesRepository): Response
    {
        return $this->render('government_parties/index.html.twig', [
            'government_parties' => $governmentPartiesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="government_parties_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $governmentParty = new GovernmentParties();
        $form = $this->createForm(GovernmentPartiesType::class, $governmentParty);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($governmentParty);
            $entityManager->flush();

            return $this->redirectToRoute('government_parties_index');
        }

        return $this->render('government_parties/new.html.twig', [
            'government_party' => $governmentParty,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="government_parties_show", methods={"GET"})
     */
    public function show(GovernmentParties $governmentParty): Response
    {
        return $this->render('government_parties/show.html.twig', [
            'government_party' => $governmentParty,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="government_parties_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, GovernmentParties $governmentParty): Response
    {
        $form = $this->createForm(GovernmentPartiesType::class, $governmentParty);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('government_parties_index');
        }

        return $this->render('government_parties/edit.html.twig', [
            'government_party' => $governmentParty,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="government_parties_delete", methods={"POST"})
     */
    public function delete(Request $request, GovernmentParties $governmentParty): Response
    {
        if ($this->isCsrfTokenValid('delete'.$governmentParty->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($governmentParty);
            $entityManager->flush();
        }

        return $this->redirectToRoute('government_parties_index');
    }
}
