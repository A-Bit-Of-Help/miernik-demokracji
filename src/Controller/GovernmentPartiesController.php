<?php

namespace App\Controller;

use App\Entity\GovernmentParties;
use App\Form\GovernmentPartiesType;
use App\Repository\DeputiesRepository;
use App\Repository\GovernmentPartiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use voku\helper\HtmlDomParser;

/**
 * @Route("/government_parties")
 */
class GovernmentPartiesController extends AbstractController
{

    public function parserGovernmentParties(GovernmentPartiesRepository $governmentPartiesRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $html = HtmlDomParser::file_get_html('http://www.sejm.gov.pl/Sejm9.nsf/page.xsp/kluby_stat');
        $elements = $html->find('td a');
        foreach ($elements as $e) {
            if ($e->innerText() != 'posłowie niezrzeszeni') {
                $name = substr($e->innerText(), 0, strpos($e->innerText(), " ("));
                $abbreviation = str_replace(
                    '</strong>',
                    '',
                    str_replace(
                        '<strong>',
                        '',
                        $e->findOne('strong')->innerHtml()
                    )
                );
            } else {
                $name = $e->innerText();
                $abbreviation = 'niez.';
            }

            $governmentParties = new GovernmentParties();
            $governmentParties->setName($name);
            $governmentParties->setAbbreviation($abbreviation);
            if ($governmentPartiesRepository->findOneBy(['name' => $name]) == null) {
                $em->persist($governmentParties);
                $em->flush();
            }
        }
    }

    /**
     * @Route("/", name="government_parties_index", methods={"GET"})
     * @param GovernmentPartiesRepository $governmentPartiesRepository
     * @return Response
     */
    public
    function index(GovernmentPartiesRepository $governmentPartiesRepository): Response
    {
        return $this->render('government_parties/index.html.twig', [
            'government_parties' => $governmentPartiesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="government_parties_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public
    function new(Request $request): Response
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
     * @param GovernmentParties $governmentParty
     * @return Response
     */
    public function show(GovernmentParties $governmentParty, DeputiesRepository $deputiesRepository): Response
    {
        $deputies = $deputiesRepository->findBy(['governmentParties' => $governmentParty->getId()]);
        return $this->render('government_parties/show.html.twig', [
            'government_party' => $governmentParty,
            "deputies" => $deputies
        ]);
    }

    /**
     * @Route("/{id}/edit", name="government_parties_edit", methods={"GET","POST"})
     * @param Request $request
     * @param GovernmentParties $governmentParty
     * @return Response
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
     * @param Request $request
     * @param GovernmentParties $governmentParty
     * @return Response
     */
    public function delete(Request $request, GovernmentParties $governmentParty): Response
    {
        if ($this->isCsrfTokenValid('delete' . $governmentParty->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($governmentParty);
            $entityManager->flush();
        }

        return $this->redirectToRoute('government_parties_index');
    }
}
