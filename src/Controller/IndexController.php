<?php


namespace PI\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{

    /**
     * @return Response
     * @Route("/", name="index")
     */
    public function index(Connection $connection)
    {
        dump($connection->executeQuery("SELECT * FROM test")->fetchAll());
        die();

        return $this->render("/index/index_page.html.twig");
    }

}
