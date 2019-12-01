<?php


namespace PI\Controller;

use PI\Employee\Exception\DoubleEntranceException;
use PI\Employee\Exception\EmployeeNotFoundException;
use PI\Employee\Guard\ApiGuardInterface;
use PI\Employee\Response\CORSResponse;
use PI\Employee\Service\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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

    /**
     * @Route("/api/getEmployeeAvatar/{fileId}", name="/api/getEmployeeAvatar")
     *
     * @param int $fileId
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getEmployeeAvatar(int $fileId)
    {
        $avatarsPath = getenv("AVATARS_PATH");
        $avatarPath = $avatarsPath . $fileId . ".jpg";

        if (file_exists($avatarPath)) {
            return $this->file($avatarPath, $fileId . ".jpg", ResponseHeaderBag::DISPOSITION_INLINE);
        } else {
            throw new FileNotFoundException("Nie znaleziono żądanego pliku!");
        }
    }

    /**
     * @Route("/api/saveEmployeeEntranceOrExit", name="/api/saveEmployeeEntranceOrExit")
     *
     * @param Request $request
     * @return CORSResponse
     * @throws \Doctrine\DBAL\DBALException
     * @throws \PI\Employee\Exception\InvalidEntranceTypeException
     */
    public function saveEmployeeEntranceOrExit(Request $request)
    {
        $post = array_merge($request->request->all(), $request->query->all());

        $photos = $request->files->all();
        $photo = null;
        if (current($photos) instanceof UploadedFile) {
            $photo = current($photos);
        }

        try
        {
            $result = $this->employeeService->insertEntranceOrExit($post["employeeId"], $post["buildingId"], $post["workplaceId"], $post["entranceType"], $photo);
        }
        catch (DoubleEntranceException $e)
        {
            return new CORSResponse([
                "status"    => self::STATUS_ERROR,
                "message"   => $e->getMessage()
            ]);
        }

        return new CORSResponse([
            "status"    => self::STATUS_OK,
            "photo_id"  => $result["photo_id"]
        ]);
    }

    /**
     * @Route("/api/getEmployeePhoto/{fileId}", name="/api/getEmployeePhoto")
     *
     * @param int $fileId
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getEmployeePhoto(int $fileId)
    {
        $entrancePhotoPath = getenv("ENTRANCE_PHOTOS");
        $entrancePhotoName = $entrancePhotoPath . $fileId . ".jpg";

        if (file_exists($entrancePhotoName)) {
            return $this->file($entrancePhotoName, $fileId . ".jpg", ResponseHeaderBag::DISPOSITION_INLINE);
        } else {
            throw new FileNotFoundException("Nie znaleziono żądanego pliku!");
        }
    }

    /**
     * @Route("/api/getEmployeeWorkingStats/{employeeId}", name="/api/getEmployeeWorkingStats/")
     *
     * @param int $employeeId
     * @return CORSResponse
     */
    public function getEmployeeWorkingStats(int $employeeId)
    {
        return new CORSResponse($this->employeeService->getEmployeeWorkingStats($employeeId, "2019-12-01 00:00:00", "2020-01-01 00:00:00"));
    }

    /**
     * @Route("/api/getCurrentEmployeesInBuildingStats/{buildingId}", name="/api/getCurrentEmployeesInBuildingStats/")
     *
     * @param int $buildingId
     * @return CORSResponse
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getCurrentEmployeesInBuildingStats(int $buildingId)
    {
        return new CORSResponse($this->employeeService->getCurrentEmployeesInBuilding($buildingId));
    }

    /**
     * @Route("/api/getEntranceImages/{employeeId}", name="/api/getEntranceImages/")
     *
     * @param Request $request
     * @param int $employeeId
     * @return CORSResponse
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

        return new CORSResponse($images);
    }

    /**
     *  Trzeba dorobic:
     *  + - routing umozliwiajacy pracownikowi wejscie/wyjsice
     *  + - routing generujacy statystyki (ile kto srednio spedzil w pracy)
     *  + - routing sprawdzajacy ilu pracownikow aktualnie jest w danym budynku
     *  - routing pobierajacy zdjecia, ktore zostaly zrobione podczas wejscia pracownikow
     */

}
