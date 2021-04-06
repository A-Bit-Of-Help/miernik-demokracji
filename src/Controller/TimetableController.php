<?php

namespace App\Controller;

use App\Entity\Timetable;
use App\Form\TimetableType;
use App\Repository\TimetableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use voku\helper\HtmlDomParser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/timetable")
 * @IsGranted("ROLE_USER")
 */
class TimetableController extends AbstractController
{
    /**
     * @param TimetableRepository $timetableRepository
     * @Route("/parser", name="deputies_parser", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function parserTimetable(TimetableRepository $timetableRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $html = HtmlDomParser::file_get_html('http://sejm.gov.pl/Sejm9.nsf/terminarz.xsp');
        $elements = $html->find('.sub-title a');
        foreach ($elements as $e){
            if(substr($e->href, 0, 4) != 'http') {
                $html2 = HtmlDomParser::file_get_html('http://sejm.gov.pl/Sejm9.nsf/terminarz.xsp' . $e->href);
                $elements2 = $html2->find('table td a');
                foreach ($elements2 as $e2){
                    $number = substr($e2->text(), 0, strpos($e2->text(), '.'));
                    $title = $e2->text();
                    $link = 'http://www.sejm.gov.pl' . $e2->href;
                    $term = intval(str_replace('/Sejm', '', substr($e2->href, 0, strpos($e2->href, '.nsf'))));
                    $timetable = new Timetable();
                    $timetable->setTitle($title);
                    $timetable->setNumber($number);
                    $timetable->setLink($link);
                    $timetable->setTerm($term);
                    if($timetableRepository->findOneBy(['number' => $number]) == null){
                        $em->persist($timetable);
                        $em->flush();
                    }
                }
            }
        }
    }

    /**
     * @Route("/", name="timetable_index", methods={"GET"})
     */
    public function index(TimetableRepository $timetableRepository): Response
    {
        return $this->render('timetable/index.html.twig', [
            'timetables' => $timetableRepository->findBy([], ['number' => 'DESC']),
        ]);
    }

    /**
     * @Route("/new", name="timetable_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $timetable = new Timetable();
        $form = $this->createForm(TimetableType::class, $timetable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($timetable);
            $entityManager->flush();

            return $this->redirectToRoute('timetable_index');
        }

        return $this->render('timetable/new.html.twig', [
            'timetable' => $timetable,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="timetable_show", methods={"GET"})
     */
    public function show(Timetable $timetable): Response
    {
        return $this->render('timetable/show.html.twig', [
            'timetable' => $timetable,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="timetable_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Timetable $timetable): Response
    {
        $form = $this->createForm(TimetableType::class, $timetable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('timetable_index');
        }

        return $this->render('timetable/edit.html.twig', [
            'timetable' => $timetable,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="timetable_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Timetable $timetable): Response
    {
        if ($this->isCsrfTokenValid('delete'.$timetable->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($timetable);
            $entityManager->flush();
        }

        return $this->redirectToRoute('timetable_index');
    }
}
