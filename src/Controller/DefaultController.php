<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @return Response
     * @Route("/xx", name="index")
     */
    public function index()
    {
        return new Response("ala ma kota");
    }

}
