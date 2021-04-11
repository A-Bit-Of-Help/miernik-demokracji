<?php

namespace App\Controller;

use App\Entity\GovernmentMeetingsDate;
use App\Form\GovernmentMeetingsDateType;
use App\Repository\GovernmentMeetingsDateRepository;
use App\Repository\TimetableRepository;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use voku\helper\HtmlDomParser;

/**
 * @Route("/government_meetings_date")
 * @IsGranted("ROLE_USER")
 */
class GovernmentMeetingsDateController extends AbstractController
{
    /**
     * @Route("/parser", name="government_meetings_date_parser", methods={"GET"})
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @param GovernmentMeetingsDateRepository $governmentMeetingsDateRepository
     * @param TimetableRepository $timetableRepository
     * @return Response
     * @throws Exception
     */
    public function parserGovernmentMeetingsDate(
        GovernmentMeetingsDateRepository $governmentMeetingsDateRepository,
        TimetableRepository $timetableRepository
    ): Response
    {
        set_time_limit(0);
        $em = $this->getDoctrine()->getManager();
        $votesOnSession = HtmlDomParser::file_get_html('http://sejm.gov.pl/Sejm9.nsf/agent.xsp?symbol=posglos&NrKadencji=9');
        $elementsOnSession = $votesOnSession->find('.left a');
        foreach ($elementsOnSession as $eOnSession) {
            $hrefDate = $eOnSession->href;
            $votesOnDate = HtmlDomParser::file_get_html('http://sejm.gov.pl/Sejm9.nsf/' . $hrefDate);
            $string = $votesOnDate->findOne('#title_content h1')->text;
            $date = new DateTime(
                str_replace('Głosowania w dniu ', '', substr($string, 0, strpos($string, ' r.')))
            );
            $governmentMeeting = $timetableRepository->findOneBy(
                ['number' => substr($votesOnDate->findOne('#title_content h1')->text, -21, 2)]
            );
            if ($governmentMeetingsDateRepository->findOneBy(['GovernmentMeeting' => $governmentMeeting, 'date' => $date]) == null) {
                $governmentMeetingDate = new GovernmentMeetingsDate();
                $governmentMeetingDate->setDate($date);
                $governmentMeetingDate->setGovernmentMeeting($governmentMeeting);
                $em->persist($governmentMeetingDate);
            }
        }

        $em->flush();

        return $this->render('government_meetings_date/index.html.twig', [
            'government_meetings_dates' => $governmentMeetingsDateRepository->findAll(),
        ]);
    }

    /**
     * @Route("/", name="government_meetings_date_index", methods={"GET"})
     * @param GovernmentMeetingsDateRepository $governmentMeetingsDateRepository
     * @return Response
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
     * @param Request $request
     * @return Response
     */
    public
    function new(Request $request): Response
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
     * @param GovernmentMeetingsDate $governmentMeetingsDate
     * @return Response
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
     * @param Request $request
     * @param GovernmentMeetingsDate $governmentMeetingsDate
     * @return Response
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
     * @param Request $request
     * @param GovernmentMeetingsDate $governmentMeetingsDate
     * @return Response
     */
    public function delete(Request $request, GovernmentMeetingsDate $governmentMeetingsDate): Response
    {
        if ($this->isCsrfTokenValid('delete' . $governmentMeetingsDate->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($governmentMeetingsDate);
            $entityManager->flush();
        }

        return $this->redirectToRoute('government_meetings_date_index');
    }
}
