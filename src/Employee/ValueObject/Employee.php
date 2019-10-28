<?php

namespace PI\Employee\ValueObject;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Employee
{
    /**
     * @var string
     */
    private $employeeId = "";

    /**
     * @var string
     */
    private $employeeFirstName = "";

    /**
     * @var string
     */
    private $employeeLastName = "";

    /**
     * @var string
     */
    private $employeeEmail = "";

    /**
     * @var string
     */
    private $employeePhone = "";

    /**
     * @var UploadedFile|null
     */
    private $avatar = null;

    /**
     * Employee constructor.
     * @param array|null $data
     */
    public function __construct(?array $data)
    {
        $this->employeeId = $data["id"] ?? "";
        $this->employeeEmail = $data["email"] ?? "";
        $this->employeeFirstName = $data["firstname"] ?? "";
        $this->employeeLastName = $data["lastname"] ?? "";
        $this->employeePhone = $data["phone"] ?? "";
        if (!empty($data["avatar"]) && $data["avatar"] instanceof UploadedFile) {
            $this->avatar = $data["avatar"];
        }
    }

    /**
     * @return string
     */
    public function getEmployeeId(): string
    {
        return $this->employeeId;
    }

    /**
     * @param string $employeeId
     */
    public function setEmployeeId(string $employeeId): void
    {
        $this->employeeId = $employeeId;
    }

    /**
     * @return string
     */
    public function getEmployeeFirstName(): string
    {
        return $this->employeeFirstName;
    }

    /**
     * @param string $employeeFirstName
     */
    public function setEmployeeFirstName(string $employeeFirstName): void
    {
        $this->employeeFirstName = $employeeFirstName;
    }

    /**
     * @return string
     */
    public function getEmployeeLastName(): string
    {
        return $this->employeeLastName;
    }

    /**
     * @param string $employeeLastName
     */
    public function setEmployeeLastName(string $employeeLastName): void
    {
        $this->employeeLastName = $employeeLastName;
    }

    /**
     * @return string
     */
    public function getEmployeeEmail(): string
    {
        return $this->employeeEmail;
    }

    /**
     * @param string $employeeEmail
     */
    public function setEmployeeEmail(string $employeeEmail): void
    {
        $this->employeeEmail = $employeeEmail;
    }

    /**
     * @return string
     */
    public function getEmployeePhone(): string
    {
        return $this->employeePhone;
    }

    /**
     * @param string $employeePhone
     */
    public function setEmployeePhone(string $employeePhone): void
    {
        $this->employeePhone = $employeePhone;
    }

    /**
     * @return UploadedFile|null
     */
    public function getAvatar() : ?UploadedFile
    {
        return $this->avatar;
    }

    /**
     * @param null $avatar
     */
    public function setAvatar($avatar): void
    {
        $this->avatar = $avatar;
    }

}
