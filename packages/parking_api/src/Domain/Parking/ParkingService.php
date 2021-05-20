<?php

namespace Concrete\Package\ParkingApi\Src\Domain\Parking;

use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlip;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlipsService;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlot;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsService;
use Concrete\Package\ParkingApi\Src\Domain\Vehicles\VehiclesService;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlipsException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlotAlreadyEmptyException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlotDoesNotExistException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlotsException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlotTypeInvalidException;

/**
 * Class ParkingService
 * @package Concrete\Package\ParkingApi\Src\Domain\Parking
 */
class ParkingService
{
    private $vehiclesService;
    private $parkingSlotsService;
    private $parkingSlipsService;

    /**
     * ParkingService constructor.
     */
    public function __construct()
    {
        $this->vehiclesService = new VehiclesService();
        $this->parkingSlotsService = new ParkingSlotsService();
        $this->parkingSlipsService = new ParkingSlipsService();
    }

    /**
     * @param $entryPoint
     * @param $plateNumber
     * @param $type
     * @param $color
     * @return ParkingSlot|mixed|null
     * @throws ParkingSlipsException
     * @throws ParkingSlotsException
     */
    public function park($entryPoint, $plateNumber, $type, $color)
    {
        $vehicle = $this->vehiclesService->add($plateNumber, $type, $color);

        $parkingSlips = $this->parkingSlipsService->getByPlateNumber($vehicle->getPlateNumber());

        if ($parkingSlips->count()) {
            $latestParkingSlip = $parkingSlips->getLatest();
            if ($latestParkingSlip && $latestParkingSlip->isOngoing()) {
                throw new ParkingSlipsException('Vehicle has not exited the parking yet.');
            }
        }

        $nearestParkingSlot = $this->parkingSlotsService->getAllAvailable()->getNearestForVehicleType($entryPoint, $vehicle->getType());

        if (!$nearestParkingSlot) {
            throw new ParkingSlotsException('No available parking slot at this time. Please try again later.');
        }

        $this->parkingSlotsService->toggleAvailability($nearestParkingSlot);

        $this->parkingSlipsService->add($nearestParkingSlot->getId(), $vehicle->getPlateNumber());

        return $nearestParkingSlot;
    }

    /**
     * @param $parkingSlotId
     * @return ParkingSlip
     * @throws ParkingSlipsException
     * @throws ParkingSlotAlreadyEmptyException
     * @throws ParkingSlotDoesNotExistException
     * @throws ParkingSlotTypeInvalidException
     */
    public function unPark($parkingSlotId)
    {
        $parkingSlot = $this->parkingSlotsService->getById($parkingSlotId);

        if (!$parkingSlot->getId()) {
            throw new ParkingSlotDoesNotExistException('Parking slot ' . $parkingSlotId . ' does not exist.');
        }

        if ($parkingSlot->getIsAvailable()) {
            throw new ParkingSlotAlreadyEmptyException('Parking slot ' . $parkingSlotId . ' is already empty.');
        }

        $this->parkingSlotsService->toggleAvailability($parkingSlot);

        $parkingSlip = $this->parkingSlipsService->getByParkingSlotId($parkingSlotId)->getLatest();

        if (!$parkingSlip) {
            throw new ParkingSlipsException('Parking slip for parking slot id ' . $parkingSlotId . ' does not exist.');
        }

        return $this->parkingSlipsService->updateParkingSlip($parkingSlip, $parkingSlot->getType());
    }
}