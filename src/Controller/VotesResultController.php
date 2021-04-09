<?php

namespace App\Controller;

use App\Entity\VotesResult;
use App\Form\VotesResultType;
use App\Repository\DeputiesRepository;
use App\Repository\TimetableRepository;
use App\Repository\VotesRepository;
use App\Repository\VotesResultRepository;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use voku\helper\HtmlDomParser;

/**
 * @Route("/votes_result")
 * @IsGranted("ROLE_USER")
 */
class VotesResultController extends AbstractController
{
    /**
     * @Route("/parser", name="votes_result_parser", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @param VotesResultRepository $votesResultRepository
     * @param DeputiesRepository $deputiesRepository
     * @param VotesRepository $votesRepository
     * @return Response
     * @throws Exception
     */
    public function parser(
        VotesResultRepository $votesResultRepository,
        DeputiesRepository $deputiesRepository,
        VotesRepository $votesRepository
    )
    {
        set_time_limit(0);

        $em = $this->getDoctrine()->getManager();
        $votesOnSession = HtmlDomParser::file_get_html('http://sejm.gov.pl/Sejm9.nsf/agent.xsp?symbol=posglos&NrKadencji=9');
        $elementsOnSession = $votesOnSession->find('.left a');
        foreach ($elementsOnSession as $eOnSession) {
            $hrefDate = $eOnSession->href;
            $votesOnDate = HtmlDomParser::file_get_html('http://sejm.gov.pl/Sejm9.nsf/' . $hrefDate);
            $sitesResult = $votesOnDate->find('.bold a');
            foreach ($sitesResult as $siteResult)
            {
                $voteOnDate = HtmlDomParser::file_get_html('http://sejm.gov.pl/Sejm9.nsf/' . $siteResult->href);
                $dateVotes = $voteOnDate->find('table .left a');

                foreach ($dateVotes as $dateVote){
                    $partiesVotesHref = $dateVote->href;
                    $votesResult = HtmlDomParser::file_get_html('http://sejm.gov.pl/Sejm9.nsf/' . $partiesVotesHref);
                    $deputiesList = $votesResult->find('tbody td');
                    $string = $votesResult->findOne('#title_content h1')->text;
                    $agendaItem = str_replace('Głosowanie nr ', '', substr($string, 0, strpos($string, ' dnia')));
                    $date = new DateTime(substr($string, -10));
                    $index = 1;
                    $name = '';
                    $vote = '';
                    foreach ($deputiesList as $deputyFor) {
                        if($index % 2 == 0){
                            $name = $deputyFor->innerText();
                        }
                        if($index % 3 == 0){
                            $vote = $deputyFor->innerText();;
                        }
                        if($index == 3){
                            $name = explode(' ', html_entity_decode($name));
                            $num = count($name);
                            $firstname = $middlename = $surname = null;
                            if ($num == 2) {
                                list($firstname, $surname) = $name;
                            } else {
                                list($firstname, $middlename, $surname) = $name;
                            }
                            $votesResult = new VotesResult();
                            $deputies = $deputiesRepository->findOneBy(['firstname' => $firstname, 'middlename' => $middlename, 'surname' => $surname]);
                            $votes = $votesRepository->findOneBy(['date' => $date, 'agendaItem' => $agendaItem]);
                            $votesResult->setDeputies($deputies);
                            $votesResult->setVoteResult($vote);
                            $votesResult->setVote($votes);
                            if($votesResultRepository->findOneBy([
                                'deputies' => $deputies,
                                'vote' => $votes
                            ]) == null) {
                                $em->persist($votesResult);
                            }

                            $index = 1;
                        } else {
                            $index++;
                        }
                    }
                    $em->flush();
                }
            }
        }
        return $this->render('votes_result/index.html.twig', [
            'votes_results' => $votesResultRepository->findAll(),
        ]);
    }

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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
