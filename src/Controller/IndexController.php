<?php


namespace PI\Controller;

use PI\Employee\Service\EmployeeService;
use PI\Employee\ValueObject\Employee;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{

    /**
     * @return Response
     * @Route("/", name="index", methods={"GET"})
     */
    public function index()
    {
        $data = [];
        $data["version_commit_hash"] = shell_exec("git rev-parse --verify HEAD");
        $data["version_commit_date"] = shell_exec("git log -1 --format=%ci --date=local");

        return $this->render("/index/index_page.html.twig", $data);
    }

    /**
     * @return Response
     * @Route("/dodaj", name="addEmployeeGet", methods={"GET"})
     */
    public function addEmployeeGet()
    {
        $data = [];

        return $this->render("/index/add_employee.html.twig", $data);
    }

    /**
     * @param Request $request
     * @param EmployeeService $employeeService
     * @return Response
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     *
     * @Route("/dodaj", name="addEmployeePost", methods={"POST"})
     */
    public function addEmployeePost(Request $request, EmployeeService $employeeService)
    {
        $post = $request->request->all();
        $avatar = current($request->files->all());

        $employee = new Employee([
            "id"        => $post["id"],
            "email"     => $post["email"],
            "firstname" => $post["name"],
            "lastname"  => $post["lastname"],
            "phone"     => $post["phone"],
            "avatar"    => $avatar
        ]);

        $employeeService->insertEmployee($employee);

        return new Response("OK. ID pracownika: " . $employee->getEmployeeId());
    }


}
