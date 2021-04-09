<?php

namespace App\Controller;

use App\Entity\GovernmentMeetingsDate;
use App\Form\GovernmentMeetingsDateType;
use App\Repository\GovernmentMeetingsDateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/government_meetings_date")
 * @IsGranted("ROLE_USER")
 */
class GovernmentMeetingsDateController extends AbstractController
{
    /**
     * @Route("/", name="government_meetings_date_index", methods={"GET"})
     */
    public function index(GovernmentMeetingsDateRepository $governmentMeetingsDateRepository): Response
    {
        return $this->render('government_meetings_date/index.html.twig', [
            'government_meetings_dates' => $governmentMeetingsDateRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="government_meetings_date_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $governmentMeetingsDate = new GovernmentMeetingsDate();
        $form = $this->createForm(GovernmentMeetingsDateType::class, $governmentMeetingsDate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($governmentMeetingsDate);
            $entityManager->flush();

            return $this->redirectToRoute('government_meetings_date_index');
        }

        return $this->render('government_meetings_date/new.html.twig', [
            'government_meetings_date' => $governmentMeetingsDate,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="government_meetings_date_show", methods={"GET"})
     */
    public function show(GovernmentMeetingsDate $governmentMeetingsDate): Response
    {
        return $this->render('government_meetings_date/show.html.twig', [
            'government_meetings_date' => $governmentMeetingsDate,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="government_meetings_date_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, GovernmentMeetingsDate $governmentMeetingsDate): Response
    {
        $form = $this->createForm(GovernmentMeetingsDateType::class, $governmentMeetingsDate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('government_meetings_date_index');
        }

        return $this->render('government_meetings_date/edit.html.twig', [
            'government_meetings_date' => $governmentMeetingsDate,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="government_meetings_date_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, GovernmentMeetingsDate $governmentMeetingsDate): Response
    {
        if ($this->isCsrfTokenValid('delete'.$governmentMeetingsDate->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($governmentMeetingsDate);
            $entityManager->flush();
        }

        return $this->redirectToRoute('government_meetings_date_index');
    }
}
