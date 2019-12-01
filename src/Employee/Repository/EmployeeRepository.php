<?php

namespace PI\Employee\Repository;

use Doctrine\DBAL\Connection;
use PI\Employee\Exception\InvalidEntranceTypeException;
use PI\Employee\ValueObject\Employee;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EmployeeRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * EmployeeRepository constructor.
     * @param Connection $connection
     */
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
            SELECT 
                employee.*
            FROM employee
            LEFT JOIN file ON employee.avatar_id = file.file_id
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

            $avatarPath = getenv("AVATARS_PATH") . $lastInsertedFileId . ".jpg";
            $avatarName = $lastInsertedFileId . ".jpg";
            $avatarContent = file_get_contents($employee->getAvatar()->getPathname());
            file_put_contents($avatarPath, $avatarContent);
            $avatarPathDb = getenv("AVATARS_PATH") . $lastInsertedFileId . ".jpg";

            $this->connection->executeQuery("
                UPDATE file 
                SET file_path = " . $this->connection->quote($avatarPathDb) . ", file_type = 'image',
                file_name = " . $this->connection->quote($avatarName) . "
                WHERE file_id = " . $this->connection->quote($lastInsertedFileId) . "
            ");

            $this->connection->executeQuery("
                UPDATE employee SET avatar_id = " . $this->connection->quote($lastInsertedFileId) . "
                WHERE employee_id = " . $this->connection->quote($employee->getEmployeeId()) . "
            ");
        }

        $this->connection->commit();
    }

    /**
     * @param int $employeeId
     * @param int $buildingId
     * @param int $workplaceId
     * @param string $entranceType
     * @param UploadedFile|null $photo
     * @throws InvalidEntranceTypeException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function insertEntranceOrExit(int $employeeId, int $buildingId, int $workplaceId, string $entranceType, ?UploadedFile $photo) : array
    {
        if (!in_array($entranceType, ["in", "out"])) {
            throw new InvalidEntranceTypeException("Zły typ wejścia! Dozwolone: ['in', 'out'], otrzymano: " . $entranceType);
        }

        $this->connection->executeQuery("
            INSERT INTO entrance (employee_id, building_id, workplace_id, entrance_type, entrance_date) VALUES (
                " . $this->connection->quote($employeeId) . ",
                " . $this->connection->quote($buildingId) . ",
                " . $this->connection->quote($workplaceId) . ",
                " . $this->connection->quote($entranceType) . ",
                NOW()
            );
        ");
        $entranceId = $this->connection->executeQuery("
            SELECT LAST_INSERT_ID() AS id;
        ")->fetch()["id"];


        $lastInsertedFileId = null;
        if ($photo != null && in_array($photo->getMimeType(), ["image/jpeg", "image/png", "image/gif"]))
        {
            $this->connection->executeQuery("
                INSERT INTO file (file_path, file_type) VALUES ('', '');
            ");
            $lastInsertedFileId = $this->connection->executeQuery("
                SELECT LAST_INSERT_ID() AS id;
            ")->fetch()["id"];

            $entrancePhotoPath = getenv("ENTRANCE_PHOTOS") . $lastInsertedFileId . ".jpg";
            $entrancePhotoName = $lastInsertedFileId . ".jpg";
            $photoContent = file_get_contents($photo->getPathname());
            file_put_contents($entrancePhotoPath, $photoContent);
            $avatarPathDb = getenv("AVATARS_PATH") . $lastInsertedFileId . ".jpg";

            $this->connection->executeQuery("
                UPDATE file 
                SET file_path = " . $this->connection->quote($avatarPathDb) . ", file_type = 'image',
                file_name = " . $this->connection->quote($entrancePhotoName) . "
                WHERE file_id = " . $this->connection->quote($lastInsertedFileId) . "
            ");

            $this->connection->executeQuery("
                UPDATE entrance SET photo_id = " . $this->connection->quote($lastInsertedFileId) . "
                WHERE entrance_id = " . $this->connection->quote($entranceId) . "
            ");
        }

        return [
            "photo_id" => $lastInsertedFileId
        ];
    }

    /**
     * @param int $employeeId
     * @return array|null
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getLastEntrance(int $employeeId) : ?array
    {
        $data = $this->connection->executeQuery("
            SELECT *
            FROM entrance
            WHERE entrance_id = (
                SELECT MAX(entrance_id) 
                FROM entrance 
                WHERE employee_id = " . $this->connection->quote($employeeId) . "
            );
        ")->fetch();

        return is_array($data) ? $data : null;
    }

    /**
     * @param int $employeeId
     * @param string $dateStart
     * @param string $dateEnd
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getEntrances(int $employeeId, string $dateStart, string $dateEnd) : array
    {
        $data = $this->connection->executeQuery("
            SELECT * 
            FROM entrance 
            WHERE 
                employee_id = " . $this->connection->quote($employeeId) . " 
                AND entrance_date >= " . $this->connection->quote($dateStart) . " 
                AND entrance_date < " . $this->connection->quote($dateEnd) . "
            ORDER BY entrance_id ASC;
            ;
        ")->fetchAll();

        return $data;
    }

    /**
     * @param int $buildingId
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCurrentEmployeesInBuilding(int $buildingId) : array
    {
        $data = $this->connection->executeQuery("
            SELECT entrance.entrance_date, e.*
            FROM entrance
            INNER JOIN employee e on entrance.employee_id = e.employee_id
            WHERE entrance_id IN (
                SELECT MAX(entrance_id) FROM entrance GROUP BY employee_id
            )
            AND building_id = " . $buildingId . " AND entrance_type = 'in';
        ")->fetchAll();

        return $data;
    }

}
