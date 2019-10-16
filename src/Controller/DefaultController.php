<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @return Response
     * @Route("/", name="index")
     */
    public function index()
    {
        return new Response("Tutaj będzie Staszkowe API");
    }

    /**
     * @return Response
     * @Route("/test", name="test")
     */
    public function test()
    {
        return $this->render("base.html.twig");
    }

}
