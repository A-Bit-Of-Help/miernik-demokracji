<?php

namespace App\Controller;

use App\Entity\GovernmentParties;
use App\Repository\GovernmentPartiesRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use voku\helper\HtmlDomParser;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="main_page")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, GovernmentPartiesRepository $governmentPartiesRepository): Response
    {
        $em = $this->getDoctrine()->getManager();
        $html = HtmlDomParser::file_get_html('http://www.sejm.gov.pl/Sejm9.nsf/page.xsp/kluby_stat');
        $elements = $html->find('td a');
        foreach ($elements as $e) {
            if($e->innerText() != 'posłowie niezrzeszeni'){
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
            if($governmentPartiesRepository->findOneBy(['name' => $name]) == null){
                $em->persist($governmentParties);
                $em->flush();
            }

        };

        return $this->render('base.html.twig', [
        ]);
    }
}
