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
        $data = [];
        $data["version_commit_hash"] = shell_exec("git rev-parse --verify HEAD");
        $data["version_commit_date"] = shell_exec("git log -1 --format=%ci --date=local");

        return $this->render("/index/index_page.html.twig", $data);
    }

}
