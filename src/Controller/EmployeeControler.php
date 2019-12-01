<?php

namespace PI\Controller;

use PI\Employee\Response\CORSResponse;
use PI\Employee\Service\EmployeeService;
use PI\Employee\ValueObject\Employee;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeControler extends AbstractController
{
    /**
     * @var EmployeeService
     */
    private $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * @return Response
     * @Route("/dodaj-pracownika", name="addEmployeeGet", methods={"GET"})
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
     * @Route("/dodaj-pracownika", name="addEmployeePost", methods={"POST"})
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

    /**
     * @Route("/statystyki-pracownika/{employeeId}", name="/statystyki-pracownika/")
     *
     * @param int $employeeId
     * @return Response
     */
    public function getEmployeeWorkingStats(int $employeeId)
    {
        $data = $this->employeeService->getEmployeeWorkingStats($employeeId, "2019-12-01 00:00:00", "2020-01-01 00:00:00");

        return $this->render("/employee/working_stats.html.twig", $data);
    }

    /**
     * @Route("/aktualnie-w-srodku/{buildingId}", name="/aktualnie-w-srodku/")
     *
     * @param int $buildingId
     * @return Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCurrentEmployeesInBuildingStats(int $buildingId)
    {
        $data = $this->employeeService->getCurrentEmployeesInBuilding($buildingId);

        return $this->render("/employee/currently_inside.html.twig", $data);
    }

    /**
     * @Route("/zdjecia-wejsc/{employeeId}", name="/zdjecia-wejsc/")
     *
     * @param Request $request
     * @param int $employeeId
     * @return Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getEntranceImages(Request $request, int $employeeId)
    {
        $entrances = $this->employeeService->getEntrances($employeeId, "2019-12-01 00:00:00", "2020-01-01 00:00:00");
        $images = [];
        foreach ($entrances as $entrance)
        {
            $images[] = $request->getSchemeAndHttpHost() . "/api/getEmployeePhoto/" . $entrance["photo_id"];
        }

        $data = [];
        $data["images"] = $images;

        return $this->render("/employee/photos.html.twig", $data);
    }

}
