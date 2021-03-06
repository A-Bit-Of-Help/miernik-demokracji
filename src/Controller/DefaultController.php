<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="main_page")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->render('base.html.twig', [
        ]);
    }

    /**
     * @Route("/no_right_permission", name="no_right_permission")
     * @param Request $request
     * @return Response
     */
    public function noRightPermission(Request $request): Response
    {
        return $this->render('security/right_permission.html.twig', [
        ]);
    }

    /**
     * @Route("/parser", name="parser_index")
     * @param Request $request
     * @return Response
     */
    public function parserSite(Request $request): Response
    {
        $parsers = [
            ['name' => 'Deputies', 'path' => 'deputies_parser', 'list' => 'deputies_index'],
            ['name' => 'Parties', 'path' => 'government_parties_parser', 'list' => 'government_parties_index'],
            ['name' => 'Timetable', 'path' => 'timetable_parser', 'list' => 'timetable_index'],
            ['name' => 'Government meetings date', 'path' => 'government_meetings_date_parser', 'list' => 'government_meetings_date_index'],
            ['name' => 'Votes', 'path' => 'votes_parser', 'list' => 'votes_index'],
            ['name' => 'Votes result', 'path' => 'votes_result_parser', 'list' => false],
        ];
        return $this->render('parser/index.html.twig', [
            'parsers' => $parsers
        ]);
    }
}
