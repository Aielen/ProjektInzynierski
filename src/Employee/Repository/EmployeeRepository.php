<?php

namespace PI\Employee\Repository;

use Doctrine\DBAL\Connection;
use PI\Employee\ValueObject\Employee;
use Symfony\Component\HttpKernel\KernelInterface;

class EmployeeRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(Connection $connection, KernelInterface $kernel)
    {
        $this->connection = $connection;
        $this->kernel = $kernel;
    }

    /**
     * @param string $employeeId
     * @return array|null
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getEmployeeInfo(string $employeeId) : ?array
    {
        $data = $this->connection->executeQuery("
            SELECT 
                employee.*, 
                REPLACE(file_path, '/public', '') AS avatar_path
            FROM employee
            INNER JOIN file ON employee.avatar_id = file.file_id
            WHERE employee_id = " . $this->connection->quote($employeeId) . "
        ")->fetch();

        if ($data == false) {
            return null;
        } else {
            return $data;
        }
    }

    /**
     * @param Employee $employee
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function insertEmployee(Employee $employee) : void
    {
        $this->connection->beginTransaction();

        $this->connection->executeQuery("
            INSERT INTO employee (employee_id, employee_firstname, employee_lastname, employee_email, employee_phone) VALUES (
                " . $this->connection->quote($employee->getEmployeeId()) . ",
                " . $this->connection->quote($employee->getEmployeeFirstName()) . ",
                " . $this->connection->quote($employee->getEmployeeLastName()) . ",
                " . $this->connection->quote($employee->getEmployeeEmail()) . ",
                " . $this->connection->quote($employee->getEmployeePhone()) . "
            )
        ");

        if ($employee->getAvatar() != null && in_array($employee->getAvatar()->getMimeType(), ["image/jpeg", "image/png", "image/gif"]))
        {
            $this->connection->executeQuery("
                INSERT INTO file (file_path, file_type) VALUES ('', '');
            ");
            $lastInsertedFileId = $this->connection->executeQuery("
                SELECT LAST_INSERT_ID() AS id;
            ")->fetch()["id"];

            $avatarPath = $this->kernel->getProjectDir() . getenv("AVATARS_PATH");
            $avatarContent = file_get_contents($employee->getAvatar()->getPathname());
            file_put_contents($avatarPath . $lastInsertedFileId . ".jpg", $avatarContent);
            $avatarPathDb = getenv("AVATARS_PATH") . $lastInsertedFileId . ".jpg";

            $this->connection->executeQuery("
                UPDATE file 
                SET file_path = " . $this->connection->quote($avatarPathDb) . ", file_type = 'image'
                WHERE file_id = " . $this->connection->quote($lastInsertedFileId) . "
            ");

            $this->connection->executeQuery("
                UPDATE employee SET avatar_id = " . $this->connection->quote($lastInsertedFileId) . "
                WHERE employee_id = " . $this->connection->quote($employee->getEmployeeId()) . "
            ");
        }

        $this->connection->commit();
    }

}
