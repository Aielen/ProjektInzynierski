<?php

namespace PI\Employee\Service;

use PI\Employee\Exception\DoubleEntranceException;
use PI\Employee\Exception\EmployeeNotFoundException;
use PI\Employee\Repository\EmployeeRepository;
use PI\Employee\ValueObject\Employee;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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

    /**
     * @param Employee $employee
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function insertEmployee(Employee $employee) : void
    {
        $this->employeeRepository->insertEmployee($employee);
    }

    /**
     * @param int $employeeId
     * @param int $buildingId
     * @param int $workplaceId
     * @param string $entranceType
     * @param UploadedFile|null $photo
     * @return array
     * @throws DoubleEntranceException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PI\Employee\Exception\InvalidEntranceTypeException
     */
    public function insertEntranceOrExit(int $employeeId, int $buildingId, int $workplaceId, string $entranceType, ?UploadedFile $photo) : array
    {
        $lastEntrance = $this->employeeRepository->getLastEntrance($employeeId);

        if ($lastEntrance != null && $lastEntrance["entrance_type"] == $entranceType)
        {
            throw new DoubleEntranceException(
                "Próbujesz 2x wejść lub wyjść! Ostatni typ wejścia: '" . $lastEntrance["entrance_type"] .
                "', data: '" . $lastEntrance["entrance_date"] . "'. Czy ostatnim razem zapomniałeś/łaś zalogować faktu wyjścia/wejścia?"
            );
        }

        return $this->employeeRepository->insertEntranceOrExit($employeeId, $buildingId, $workplaceId, $entranceType, $photo);
    }

    public function getEmployeeWorkingStats(int $employeeId, string $dateStart, string $dateEnd) : array
    {
        $entrances = $this->employeeRepository->getEntrances($employeeId, $dateStart, $dateEnd);

        foreach ($entrances as $index => $entrance)
        {
            if ($entrance["entrance_type"] == "in") {
                break;
            } else {
                unset($entrances[$index]);
            }
        }

        $lastIn = 0;
        $lastOut = 0;
        $totalTime = 0;
        $i = 1;
        $daysIn = [];

        foreach ($entrances as $entrance)
        {
            $daysIn[] = date('D', strtotime($entrance["entrance_date"]));

            if ($entrance["entrance_type"] == "in") {
                $lastIn = strtotime($entrance["entrance_date"]);
            } else {
                $lastOut = strtotime($entrance["entrance_date"]);
            }

            if ($i % 2 == 0) {
                $totalTime += ($lastOut - $lastIn);
            }

            $i++;
        }

        return [
            "totalTimeInSeconds"    => $totalTime,
            "totalTimeString"       => $this->formatTime($totalTime),
            "averageTime"           => $totalTime / count(array_unique($daysIn)),
            "averageTimeString"     => $this->formatTime($totalTime / count(array_unique($daysIn))),
            "daysIn"                => count(array_unique($daysIn))
        ];
    }

    /**
     * @param int $seconds
     * @return string
     */
    public function formatTime(int $seconds) : string
    {
        $days    = floor($seconds / 86400);
        $hours   = floor(($seconds - ($days * 86400)) / 3600);
        $minutes = floor(($seconds - ($days * 86400) - ($hours * 3600)) / 60);
        $seconds2 = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes * 60)));

        return $days . " dni " . $hours . " godzin " . $minutes . " minut " . $seconds2 . " sekund";
    }

    /**
     * @param int $buildingId
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCurrentEmployeesInBuilding(int $buildingId) : array
    {
        $data = $this->employeeRepository->getCurrentEmployeesInBuilding($buildingId);

        return [
            "employeeList" => $data,
            "employeesIn"  => count($data)
        ];
    }

    /**
     * @param int $employeeId
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getEntrances(int $employeeId, string $startDate, string $endDate)
    {
        $data = $this->employeeRepository->getEntrances($employeeId, $startDate, $endDate);

        return $data;
    }

}
