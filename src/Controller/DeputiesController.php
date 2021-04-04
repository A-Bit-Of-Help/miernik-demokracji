<?php

namespace App\Controller;

use App\Entity\Deputies;
use App\Form\DeputiesType;
use App\Repository\DeputiesRepository;
use App\Repository\GovernmentPartiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use voku\helper\HtmlDomParser;

/**
 * @Route("/deputies")
 */
class DeputiesController extends AbstractController
{
    public function parserDeputies(GovernmentPartiesRepository $governmentPartiesRepository,
                                   DeputiesRepository $deputiesRepository
    )
    {
        $em = $this->getDoctrine()->getManager();
        $html = HtmlDomParser::file_get_html('http://www.sejm.gov.pl/Sejm9.nsf/poslowie.xsp?type=A');
        $elements = $html->find('.deputies li');
        foreach ($elements as $e) {
            $name = explode(' ', html_entity_decode($e->findOne('.deputyName')->innerText()));
            $num = count($name);
            $firstname = $middlename = $surname = null;

            if ($num == 2) {
                list($firstname, $surname) = $name;
            } else {
                list($firstname, $middlename, $surname) = $name;
            }

            $abbreviation = str_replace(
                '</strong>',
                '',
                str_replace(
                    '<strong>',
                    '',
                    $e->findOne('strong')->innerHtml()
                )
            );
            foreach($e->find('a') as $a){
                $link = 'http://www.sejm.gov.pl' . $a->href;
            };

            $governmentParties = $governmentPartiesRepository->findOneBy(['abbreviation' => $abbreviation]);

            $deputies = new Deputies();
            $deputies->setFirstname($firstname);
            $deputies->setMiddlename($middlename);
            $deputies->setSurname($surname);
            $deputies->setLink($link);
            $deputies->setGovernmentParties($governmentParties);
            if($deputiesRepository->findOneBy(['firstname' => $firstname, 'middlename' => $middlename, 'surname' => $surname]) == null){
                $em->persist($deputies);
                $em->flush();
            }

        };
    }

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
