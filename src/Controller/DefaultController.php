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
     * @Route("/test2", name="test2")
     */
    public function test2()
    {
        return $this->render("base.html.twig");
    }

}
