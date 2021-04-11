<?php

namespace App\Controller;

use App\Entity\VotesResult;
use App\Form\VotesResultType;
use App\Repository\DeputiesRepository;
use App\Repository\VotesRepository;
use App\Repository\VotesResultRepository;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
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
     * @IsGranted("ROLE_SUPER_ADMIN")
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
    ): Response
    {
        set_time_limit(0);

        $em = $this->getDoctrine()->getManager();
        $votes = $votesRepository->findBy(['parser' => false]);

        foreach ($votes as $vote) {
            $voteOnDate = HtmlDomParser::file_get_html($vote->getLink());
            $dateVotes = $voteOnDate->find('table .left a');

            foreach ($dateVotes as $dateVote) {
                $partiesVotesHref = $dateVote->href;
                $votesResult = HtmlDomParser::file_get_html('http://sejm.gov.pl/Sejm9.nsf/' . $partiesVotesHref);
                $deputiesList = $votesResult->find('tbody td');
                $index = 1;
                $name = '';
                $voteResult = '';
                foreach ($deputiesList as $deputyFor) {
                    if ($index % 2 == 0) {
                        $name = $deputyFor->innerText();
                    }
                    if ($index % 3 == 0) {
                        $voteResult = $deputyFor->innerText();;
                    }
                    if ($index == 3) {
                        $name = explode(' ', html_entity_decode($name));
                        $num = count($name);
                        $firstname = $middlename = $surname = null;
                        if ($num == 2) {
                            list($surname, $firstname) = $name;
                        } else {
                            list($surname, $middlename, $firstname) = $name;
                        }
                        $votesResult = new VotesResult();
                        $deputies = $deputiesRepository->findOneBy(['firstname' => $firstname, 'middlename' => $middlename, 'surname' => $surname]);
                        $votesResult->setDeputies($deputies);
                        $votesResult->setVoteResult($voteResult);
                        $votesResult->setVote($vote);

                        if ($votesResultRepository->findOneBy([
                                'deputies' => $deputies,
                                'vote' => $vote
                            ]) == null) {
                            $em->persist($votesResult);
                        }
                        $index = 1;
                    } else {
                        $index++;
                    }
                }
                $vote->setParser(true);
                $em->persist($vote);
            }
            $em->flush();
        }
        return $this->render('votes_result/index.html.twig', [
            'votes_results' => $votesResultRepository->findAll(),
        ]);
    }

    /**
     * @Route("/vote/{id}", name="votes_result_vote", methods={"GET"})
     * @param VotesRepository $votesRepository
     * @param VotesResultRepository $votesResultRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function index(
        VotesRepository $votesRepository,
        VotesResultRepository $votesResultRepository,
        PaginatorInterface $paginator,
        Request $request, $id
    ): Response
    {
        $votesResult = $votesResultRepository->findBy(['vote' => $id]);
        $vote = $votesRepository->find($id);
        $pagination = $paginator->paginate($votesResult, $request->query->getInt('page', 1), 30);
        return $this->render('votes_result/index.html.twig', [
            'pagination' => $pagination,
            'vote' => $vote,
        ]);
    }

    /**
     * @Route("/new", name="votes_result_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
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
     * @param VotesResult $votesResult
     * @return Response
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
     * @param Request $request
     * @param VotesResult $votesResult
     * @return Response
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
     * @param Request $request
     * @param VotesResult $votesResult
     * @return Response
     */
    public function delete(Request $request, VotesResult $votesResult): Response
    {
        if ($this->isCsrfTokenValid('delete' . $votesResult->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($votesResult);
            $entityManager->flush();
        }

        return $this->redirectToRoute('votes_result_index');
    }
}
