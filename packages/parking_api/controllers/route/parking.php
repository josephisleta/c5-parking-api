<?php

namespace Concrete\Package\ParkingApi\Controller\Route;

use Concrete\Core\Controller\Controller;
use Concrete\Package\ParkingApi\Src\Domain\Parking\ParkingService;
use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMapService;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsService;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingMissingArgumentException;

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
     */
    public function getParkingInfo()
    {
        $parkingMapService = new ParkingMapService();
        $parkingMapService->getEntryOrExitQuantity();

        $data = [
            'entryOrExitQuantity' => $parkingMapService->getEntryOrExitQuantity(),
            'status' => 200
        ];

        echo json_encode($data);
        exit();
    }

    /**
     * API controller for /api/parking/slots
     * Response (json):
     *  parking slots
     */
    public function getParkingSlots()
    {
        try {
            $parkingSlotsService = new ParkingSlotsService();

            if ($this->request('entryPoint')) {
                $parkingMapService = new ParkingMapService();
                $parkingMapService->checkIfValidEntryPoint($this->request('entryPoint'));
                $parkingSlots = $parkingSlotsService->getParkingSlotsWithDetails()->sortByEntryPoint($this->request('entryPoint'))->toArray($this->request('entryPoint'));
            } else {
                $parkingSlots = $parkingSlotsService->getParkingSlotsWithDetails()->toArray();
            }

            $data = [
                'parkingSlots' => $parkingSlots,
                'status' => 200
            ];
        } catch (\Exception $e) {
            $data = [
                'errorMessage' => $e->getMessage(),
                'status' => 300
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
            if (!$this->request('entryPoint') || !$this->request('plateNumber') || !$this->request('type')) {
                throw new ParkingMissingArgumentException('Please enter all required parameters');
            }

            $parkingService = new ParkingService();

            $parkingSlot = $parkingService->park(
                $this->request('entryPoint'),
                $this->request('plateNumber'),
                $this->request('type'),
                $this->request('color')
            );

            $data = [
                'parkingSlotId' => $parkingSlot->getId(),
                'status' => 200
            ];
        } catch (\Exception $e) {
            $data = [
                'errorMessage' => $e->getMessage(),
                'status' => 300
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
            if (!$this->get('parkingSlotId')) {
                throw new ParkingMissingArgumentException('Please enter the parkingSlotId');
            }
            $parkingService = new ParkingService();
            $parkingSlip = $parkingService->unPark($this->get('parkingSlotId'));

            $data = [
                'parkingSlip' => $parkingSlip->toArray(),
                'status' => 200
            ];
        } catch (\Exception $e) {
            $data = [
                'errorMessage' => $e->getMessage(),
                'status' => 300
            ];
        }

        echo json_encode($data);
        exit();
    }
}