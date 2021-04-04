<?php

namespace App\Controller;

use App\Entity\Deputies;
use App\Form\DeputiesType;
use App\Repository\DeputiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/deputies")
 */
class DeputiesController extends AbstractController
{
    /**
     * @Route("/", name="deputies_index", methods={"GET"})
     */
    public function index(DeputiesRepository $deputiesRepository): Response
    {
        return $this->render('deputies/index.html.twig', [
            'deputies' => $deputiesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="deputies_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $deputy = new Deputies();
        $form = $this->createForm(DeputiesType::class, $deputy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($deputy);
            $entityManager->flush();

            return $this->redirectToRoute('deputies_index');
        }

        return $this->render('deputies/new.html.twig', [
            'deputy' => $deputy,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="deputies_show", methods={"GET"})
     */
    public function show(Deputies $deputy): Response
    {
        return $this->render('deputies/show.html.twig', [
            'deputy' => $deputy,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="deputies_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Deputies $deputy): Response
    {
        $form = $this->createForm(DeputiesType::class, $deputy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('deputies_index');
        }

        return $this->render('deputies/edit.html.twig', [
            'deputy' => $deputy,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="deputies_delete", methods={"POST"})
     */
    public function delete(Request $request, Deputies $deputy): Response
    {
        if ($this->isCsrfTokenValid('delete'.$deputy->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($deputy);
            $entityManager->flush();
        }

        return $this->redirectToRoute('deputies_index');
    }
}
