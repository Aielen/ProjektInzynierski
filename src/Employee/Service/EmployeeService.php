<?php

namespace PI\Employee\Service;

use PI\Employee\Exception\EmployeeNotFoundException;
use PI\Employee\Repository\EmployeeRepository;

class EmployeeService
{
    /**
     * @var EmployeeRepository
     */
    private $employeeRepository;

    /**
     * EmployeeService constructor.
     * @param EmployeeRepository $employeeRepository
     */
    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * @param string $employeeId
     * @return array
     * @throws EmployeeNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getEmployeeInfo(string $employeeId) : array
    {
        $employee = $this->employeeRepository->getEmployeeInfo($employeeId);

        if ($employee == null) {
            throw new EmployeeNotFoundException("Pracownik o podanym id: {$employeeId} nie istnieje!");
        }

        return $employee;
    }

}
