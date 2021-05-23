<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingFunctions;

use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMapDao;
use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMapService;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlip;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlipsDao;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlipsService;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlot;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsDao;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsService;
use Concrete\Package\ParkingApi\Src\Domain\Vehicles\VehiclesDao;
use Concrete\Package\ParkingApi\Src\Domain\Vehicles\VehiclesService;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingFunctionsParkColorInvalidException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingFunctionsMissingArgumentException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingFunctionsParkPlateNumberInvalidException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingFunctionsParkVehicleTypeInvalidException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingMapInvalidEntryPointException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlipsDoesNotExistException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlipsStillActiveException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlotAlreadyEmptyException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlotDoesNotExistException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlotsException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlotTypeInvalidException;

/**
 * Class ParkingFunctionsService
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingFunctions
 */
class ParkingFunctionsService
{
    private $parkingMapService;
    private $parkingSlotsService;
    private $vehiclesService;
    private $parkingSlipsService;

    /**
     * ParkingFunctionsService constructor.
     * @param ParkingMapDao $parkingMapDao
     * @param ParkingSlotsDao $parkingSlotsDao
     * @param VehiclesDao $vehiclesDao
     * @param ParkingSlipsDao $parkingSlipsDao
     */
    public function __construct(ParkingMapDao $parkingMapDao, ParkingSlotsDao $parkingSlotsDao, VehiclesDao $vehiclesDao, ParkingSlipsDao $parkingSlipsDao)
    {
        $this->parkingMapService = new ParkingMapService($parkingMapDao);
        $this->parkingSlotsService = new ParkingSlotsService($parkingSlotsDao);
        $this->vehiclesService = new VehiclesService($vehiclesDao);
        $this->parkingSlipsService = new ParkingSlipsService($parkingSlipsDao);
    }

    /**
     * @param $entryPoint
     * @param $plateNumber
     * @param $type
     * @param $color
     * @return ParkingSlot|mixed|null
     * @throws ParkingSlipsStillActiveException
     * @throws ParkingSlotsException
     */
    public function park($entryPoint, $plateNumber, $type, $color)
    {
        $vehicle = $this->vehiclesService->add($plateNumber, $type, $color);

        $parkingSlips = $this->parkingSlipsService->getByPlateNumber($vehicle->getPlateNumber());

        if ($parkingSlips->count()) {
            $latestParkingSlip = $parkingSlips->getLatest();
            if ($latestParkingSlip && $latestParkingSlip->isOngoing()) {
                throw new ParkingSlipsStillActiveException('Vehicle has not exited the parking yet.');
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
     * @throws ParkingSlipsDoesNotExistException
     * @throws ParkingSlotTypeInvalidException
     */
    public function unPark($parkingSlotId)
    {
        $parkingSlot = $this->parkingSlotsService->getById($parkingSlotId);

        $this->parkingSlotsService->toggleAvailability($parkingSlot);

        $parkingSlip = $this->parkingSlipsService->getByParkingSlotId($parkingSlotId)->getLatest();

        if (!$parkingSlip) {
            throw new ParkingSlipsDoesNotExistException('Parking slip for parking slot id ' . $parkingSlotId . ' does not exist.');
        }

        return $this->parkingSlipsService->updateParkingSlip($parkingSlip, $parkingSlot->getType());
    }

    /**
     * @param $entryPoint
     * @param $plateNumber
     * @param $type
     * @param $color
     * @throws ParkingFunctionsParkColorInvalidException
     * @throws ParkingFunctionsMissingArgumentException
     * @throws ParkingFunctionsParkPlateNumberInvalidException
     * @throws ParkingFunctionsParkVehicleTypeInvalidException
     * @throws ParkingMapInvalidEntryPointException
     */
    public function validateParkRequest($entryPoint, $plateNumber, $type, $color)
    {
        if (!$entryPoint || !$plateNumber || !$type) {
            throw new ParkingFunctionsMissingArgumentException('Please enter all required parameters');
        }

        $this->parkingMapService->validateEntryPoint($entryPoint);

        if (!ctype_alnum($plateNumber)) {
            throw new ParkingFunctionsParkPlateNumberInvalidException('Please enter a valid plate number (alphanumeric)');
        }

        if (!in_array($type, ['S', 'M', 'L'])) {
            throw new ParkingFunctionsParkVehicleTypeInvalidException('Please enter a valid vehicle type');
        }

        if ($color && !preg_match('/^[a-zA-Z ]*$/', $color)) {
            throw new ParkingFunctionsParkColorInvalidException('Please enter a valid color');
        }
    }

    /**
     * @param $parkingSlotId
     * @throws ParkingFunctionsMissingArgumentException
     * @throws ParkingSlotAlreadyEmptyException
     * @throws ParkingSlotDoesNotExistException
     */
    public function validateUnParkRequest($parkingSlotId)
    {
        if (!$parkingSlotId) {
            throw new ParkingFunctionsMissingArgumentException('Please enter the parking slot id');
        }

        $parkingSlot = $this->parkingSlotsService->getById($parkingSlotId);

        if (!$parkingSlot->getId()) {
            throw new ParkingSlotDoesNotExistException('Parking slot id ' . $parkingSlotId . ' does not exist.');
        }

        if ($parkingSlot->getIsAvailable()) {
            throw new ParkingSlotAlreadyEmptyException('Parking slot id ' . $parkingSlotId . ' is already empty.');
        }
    }
}