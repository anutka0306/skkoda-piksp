<?php

namespace App\Controller;

use App\Rss\Xml;
use App\Repository\NaschirabotyRepository;
use App\Entity\Naschiraboty;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RssController extends AbstractController
{
    /**
     * @Route("/rss.xml", name="rss")
     * @param  NaschirabotyRepository $posts
     * @return Response
     */
    public function index(NaschirabotyRepository $naschiraboty_repository): Response
    {
        $posts = $naschiraboty_repository->findAll();
        $response = new Response();
        $response->headers->set("Content-type", "application/xhtml+xml");
        $response->setContent(Xml::generate($posts));
        return $response;
    }
}
