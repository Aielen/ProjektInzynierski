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

    /**
     * @Route("/v", name="current_version")
     */
    public function currentVersion()
    {
        $version = shell_exec("git rev-parse --verify HEAD");
        return new Response($version);
    }

}
