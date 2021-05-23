<?php

namespace Concrete\Package\ParkingApi\Controller\Route;

use Concrete\Core\Controller\Controller;
use Concrete\Package\ParkingApi\Src\Dao\ParkingMap\ParkingMapDaoImpl;
use Concrete\Package\ParkingApi\Src\Dao\ParkingSlips\ParkingSlipDaoImpl;
use Concrete\Package\ParkingApi\Src\Dao\ParkingSlots\ParkingSlotsDaoImpl;
use Concrete\Package\ParkingApi\Src\Dao\Vehicles\VehiclesDaoImpl;
use Concrete\Package\ParkingApi\Src\Domain\ParkingFunctions\ParkingFunctionsService;
use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMapService;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsService;

/**
 * Class Parking
 * Holds all parking routes
 * @package Concrete\Package\ParkingApi\Controller\Route
 */
class Parking extends Controller
{
    /**
     * API controller for /api/parking
     * Response (json):
     *  entry/exit quantity
     *  parking slots
     */
    public function getParkingInfo()
    {
        try {
            $parkingSlotsDao = new ParkingSlotsDaoImpl();
            $parkingSlotsService = new ParkingSlotsService($parkingSlotsDao);

            $parkingMapDao = new ParkingMapDaoImpl();
            $parkingMapService = new ParkingMapService($parkingMapDao);

            if ($this->request('entryPoint')) {
                $parkingMapService->validateEntryPoint($this->request('entryPoint'));
                $parkingSlots = $parkingSlotsService->getParkingSlotsWithDetails()->sortByEntryPoint($this->request('entryPoint'))->toArray($this->request('entryPoint'));
            } else {
                $parkingSlots = $parkingSlotsService->getParkingSlotsWithDetails()->toArray();
            }

            $data = [
                'entryOrExitQuantity' => $parkingMapService->getEntryOrExitQuantity(),
                'parkingSlots' => $parkingSlots,
                'status' => 200
            ];
        } catch (\Exception $e) {
            $data = [
                'errorMessage' => $e->getMessage(),
                'errorCode' => $e->getCode()
            ];
        }

        echo json_encode($data);
        exit();
    }

    /**
     * API controller for /api/parking/enter
     * Required params:
     *  plateNumber
     *  type
     *  entryPoint
     * Optional param:
     *  color
     * Response (json):
     *  parking slot id
     */
    public function enterParking()
    {
        try {
            $parkingMapDao = new ParkingMapDaoImpl();
            $parkingSlotsDao = new ParkingSlotsDaoImpl();
            $vehiclesDao = new VehiclesDaoImpl();
            $parkingSlipsDao = new ParkingSlipDaoImpl();

            $parkingFunctionsService = new ParkingFunctionsService($parkingMapDao, $parkingSlotsDao, $vehiclesDao, $parkingSlipsDao);

            $parkingFunctionsService->validateParkRequest(
                $this->request('entryPoint'),
                $this->request('plateNumber'),
                $this->request('type'),
                $this->request('color'));

            $parkingSlot = $parkingFunctionsService->park(
                $this->request('entryPoint'),
                $this->request('plateNumber'),
                $this->request('type'),
                $this->request('color'));

            $data = [
                'parkingSlotId' => $parkingSlot->getId(),
                'status' => 200
            ];
        } catch (\Exception $e) {
            $data = [
                'errorMessage' => $e->getMessage(),
                'errorCode' => $e->getCode()
            ];
        }

        echo json_encode($data);
        exit();
    }

    /**
     * API controller for /api/parking/exit
     * Required params:
     *  parkingSlotId
     * Response (json):
     *  parking fee
     */
    public function exitParking()
    {
        try {
            $parkingMapDao = new ParkingMapDaoImpl();
            $parkingSlotsDao = new ParkingSlotsDaoImpl();
            $vehiclesDao = new VehiclesDaoImpl();
            $parkingSlipsDao = new ParkingSlipDaoImpl();

            $parkingFunctionsService = new ParkingFunctionsService($parkingMapDao, $parkingSlotsDao, $vehiclesDao, $parkingSlipsDao);
            $parkingFunctionsService->validateUnParkRequest($this->request('parkingSlotId'));

            $parkingSlip = $parkingFunctionsService->unPark($this->request('parkingSlotId'));

            $data = [
                'parkingSlip' => $parkingSlip->toArray(),
                'status' => 200
            ];
        } catch (\Exception $e) {
            $data = [
                'errorMessage' => $e->getMessage(),
                'errorCode' => $e->getCode()
            ];
        }

        echo json_encode($data);
        exit();
    }
}