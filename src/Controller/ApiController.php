<?php


namespace PI\Controller;

use PI\Employee\Exception\EmployeeNotFoundException;
use PI\Employee\Guard\ApiGuardInterface;
use PI\Employee\Response\CORSResponse;
use PI\Employee\Service\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController implements ApiGuardInterface
{
    const STATUS_OK = "ok";
    const STATUS_ERROR = "error";
    const API_KEY = "QE7MMz9TnyA7GdrKCp4KFuVMwmnwMnuQ";

    /**
     * @var EmployeeService
     */
    private $employeeService;

    /**
     * ApiController constructor.
     * @param EmployeeService $employeeService
     */
    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PI\Employee\Exception\EmployeeNotFoundException
     *
     * @Route("/api/getEmployeeInfo", name="api/getEmployeeInfo")
     */
    public function getEmployeeInfo(Request $request)
    {
        try
        {
            $post = array_merge($request->request->all(), $request->query->all());
            $employee = $this->employeeService->getEmployeeInfo($post["employeeId"]);

            return new CORSResponse([
                "status"    => self::STATUS_OK,
                "employee"  => $employee
            ]);
        }
        catch (EmployeeNotFoundException $e)
        {
            return new CORSResponse([
                "status"    => self::STATUS_ERROR,
                "message"   => "Pracownik o żądanym ID nie istnieje"
            ]);
        }
    }

}
