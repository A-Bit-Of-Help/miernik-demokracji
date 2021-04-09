<?php

namespace App\Controller;

use App\Entity\Votes;
use App\Form\VotesType;
use App\Repository\TimetableRepository;
use App\Repository\VotesRepository;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use voku\helper\HtmlDomParser;

/**
 * @Route("/votes")
 * @IsGranted("ROLE_USER")
 */
class VotesController extends AbstractController
{
    /**
     * @Route("/parser", name="votes_parser", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @param VotesRepository $votesRepository
     * @param TimetableRepository $timetableRepository
     * @return Response
     * @throws Exception
     */
    public function parserVotes(VotesRepository $votesRepository, TimetableRepository $timetableRepository): Response
    {
        set_time_limit(0);
        $em = $this->getDoctrine()->getManager();
        $votesOnSession = HtmlDomParser::file_get_html('http://sejm.gov.pl/Sejm9.nsf/agent.xsp?symbol=posglos&NrKadencji=9');
        $elementsOnSession = $votesOnSession->find('tbody td');
        foreach ($elementsOnSession as $eOnSession) {
            $hrefDates = $eOnSession->find('a');
            foreach ($hrefDates as $hrefDate) {
                $votesOnDate = HtmlDomParser::file_get_html('http://sejm.gov.pl/Sejm9.nsf/' . $hrefDate->href);
                $string = $votesOnDate->findOne('#title_content h1')->text;
                $term = $timetableRepository->findOneBy(['number' => substr($string, -21, 2)]);
                $date = new DateTime(
                    str_replace('Głosowania w dniu ', '', substr($string, 0, strpos($string, ' r.')))
                );
                $elementsOnDate = $votesOnDate->findOne('tbody');
                foreach ($elementsOnDate as $eOnDate) {
                    $data = $eOnDate->find('td')->text;
                    $agendaItem = $data[0];
                    $hour = date_create_from_format('H:i:s', $data[1]);
                    $title = $data[2];
                    if ($votesRepository->findOneBy(['term' => $term, 'hour' => $hour]) == null) {
                        $votes = new Votes();
                        $votes->setDate($date);
                        $votes->setTerm($term);
                        $votes->setHour($hour);
                        $votes->setTitle($title);
                        $votes->setAgendaItem($agendaItem);
                        $votes->setLink('http://sejm.gov.pl/Sejm9.nsf/' . $hrefDate->href);
                        $em->persist($votes);
                    }
                }
            }
        }
        $em->flush();
        return $this->render('votes/index.html.twig', [
            'votes' => $votesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/", name="votes_index", methods={"GET"})
     * @param VotesRepository $votesRepository
     * @return Response
     */
    public function index(VotesRepository $votesRepository): Response
    {
        return $this->render('votes/index.html.twig', [
            'votes' => $votesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/date/{date}", name="votes_date", methods={"GET"})
     * @param VotesRepository $votesRepository
     * @param $date
     * @throws Exception
     * @return Response
     */
    public function votesOnDate(VotesRepository $votesRepository, $date): Response
    {
        $date = new DateTime($date);
        $votes = $votesRepository->findBy(['date' => $date]);
        return $this->render('votes/index.html.twig', [
            'votes' => $votes,
        ]);
    }

    /**
     * @Route("/new", name="votes_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
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
     * @param Votes $vote
     * @return Response
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
     * @param Request $request
     * @param Votes $vote
     * @return Response
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
     * @param Request $request
     * @param Votes $vote
     * @return Response
     */
    public function delete(Request $request, Votes $vote): Response
    {
        if ($this->isCsrfTokenValid('delete' . $vote->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($vote);
            $entityManager->flush();
        }

        return $this->redirectToRoute('votes_index');
    }
}
