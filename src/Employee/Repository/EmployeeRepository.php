<?php

namespace PI\Employee\Repository;

use Doctrine\DBAL\Connection;

class EmployeeRepository
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $employeeId
     * @return array|null
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getEmployeeInfo(string $employeeId) : ?array
    {
        $data = $this->connection->executeQuery("
            SELECT * FROM employee WHERE employee_id = " . $this->connection->quote($employeeId) . "
        ")->fetch();

        if ($data == false) {
            return null;
        } else {
            return $data;
        }
    }

}
