<?php


namespace PI\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{

    /**
     * @return Response
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render("/index/index_page.html.twig");
    }

}
