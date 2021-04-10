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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/deputies")
 * @IsGranted("ROLE_USER")
 */
class DeputiesController extends AbstractController
{
    /**
     * @Route("/parser", name="deputies_parser", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * @param DeputiesRepository $deputiesRepository
     * @return Response
     */
    public function parserDeputies(DeputiesRepository $deputiesRepository, GovernmentPartiesRepository $governmentPartiesRepository): Response
    {
        $linkArr = [
            ['http://www.sejm.gov.pl/Sejm9.nsf/poslowie.xsp?type=B', false],
            ['http://www.sejm.gov.pl/Sejm9.nsf/poslowie.xsp?type=A', true],
            ['http://www.sejm.gov.pl/Sejm9.nsf/poslowie.xsp?type=N', true]
        ];
        foreach ($linkArr as $link){
            $html = HtmlDomParser::file_get_html($link[0]);
            $elements = $html->find('.deputies li');
            $this->parserGetDeputies($elements, $link[1], $governmentPartiesRepository, $deputiesRepository);
        }

        return $this->redirectToRoute('deputies_index');
    }

    /**
     * @param GovernmentPartiesRepository $governmentPartiesRepository
     * @param DeputiesRepository $deputiesRepository
     * @param $elements
     */
    public function parserGetDeputies($elements, $active, GovernmentPartiesRepository $governmentPartiesRepository,
                                      DeputiesRepository $deputiesRepository)
    {
        $em = $this->getDoctrine()->getManager();

        foreach ($elements as $e) {
            $name = explode(' ', html_entity_decode($e->findOne('.deputyName')->innerText()));
            $detail = html_entity_decode($e->findOne('.deputy-box-details')->innerText());
            $details = str_replace('<br>', '', substr($detail, strpos($detail, '<br>') + 4));

            $num = count($name);
            $firstname = $middlename = $surname = null;

            if ($num == 2) {
                list($surname, $firstname) = $name;
            } else {
                list($surname, $middlename, $firstname) = $name;
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
            foreach ($e->find('a') as $a) {
                $link = 'http://www.sejm.gov.pl' . $a->href;
            };
            $html2 = HtmlDomParser::file_get_html($link);
            foreach ($html2->find('img') as $e2) {
                $photo = $e2->src;
            }

            $governmentParties = $governmentPartiesRepository->findOneBy(['abbreviation' => $abbreviation]);
            if ($deputiesRepository->findOneBy(['firstname' => $firstname, 'middlename' => $middlename, 'surname' => $surname]) == null) {
                $deputies = new Deputies();
                $deputies->setFirstname($firstname);
                $deputies->setMiddlename($middlename);
                $deputies->setSurname($surname);

            } else {
                $deputies = $deputiesRepository->findOneBy(['firstname' => $firstname, 'middlename' => $middlename, 'surname' => $surname]);
            }
            $deputies->setLink($link);
            $deputies->setGovernmentParties($governmentParties);
            $deputies->setPhoto($photo);
            $deputies->setDetails($details);
            $deputies->setActive($active);

            $em->persist($deputies);
        };
        $em->flush();
    }

    /**
     * @Route("/", name="deputies_index", methods={"GET"})
     * @param DeputiesRepository $deputiesRepository
     * @return Response
     */
    public function index(DeputiesRepository $deputiesRepository): Response
    {
        return $this->render('deputies/index.html.twig', [
            'deputies' => $deputiesRepository->findBy(['active' => true]),
        ]);
    }

    /**
     * @Route("/new", name="deputies_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @return Response
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
     * @param Deputies $deputy
     * @return Response
     */
    public function show(Deputies $deputy): Response
    {
        return $this->render('deputies/show.html.twig', [
            'deputy' => $deputy,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="deputies_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param Deputies $deputy
     * @return Response
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
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param Deputies $deputy
     * @return Response
     */
    public function delete(Request $request, Deputies $deputy): Response
    {
        if ($this->isCsrfTokenValid('delete' . $deputy->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($deputy);
            $entityManager->flush();
        }

        return $this->redirectToRoute('deputies_index');
    }
}
