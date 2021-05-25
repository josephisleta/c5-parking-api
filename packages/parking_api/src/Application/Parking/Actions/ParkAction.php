<?php

namespace Concrete\Package\ParkingApi\Src\Application\Parking\Actions;

use Concrete\Package\ParkingApi\Src\Application\Parking\Requests\ParkRequest as Request;
use Concrete\Package\ParkingApi\Src\Application\Parking\Responses\ParkResponse;
use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMapDao;
use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMapService;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlipsDao;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlipsService;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsDao;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsService;
use Concrete\Package\ParkingApi\Src\Domain\Vehicles\VehiclesDao;
use Concrete\Package\ParkingApi\Src\Domain\Vehicles\VehiclesService;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingActionInvalidRequestException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingFunctionsParkColorInvalidException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingFunctionsMissingArgumentException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingFunctionsParkPlateNumberInvalidException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingFunctionsParkVehicleTypeInvalidException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingMapInvalidEntryPointException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlipsStillActiveException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlotsException;

/**
 * Class Park
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingActions
 */
class ParkAction implements Action
{
    private $parkingMapService;
    private $parkingSlotsService;
    private $vehiclesService;
    private $parkingSlipsService;

    /**
     * Park constructor.
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
     * @param Request $request
     * @return ParkResponse
     */
    public function process($request)
    {
        try {
            $this->validate($request);

            $vehicle = $this->vehiclesService->add($request->getPlateNumber(), $request->getType(), $request->getColor());

            $latestParkingSlip = $this->parkingSlipsService->getLatestByPlateNumber($vehicle->getPlateNumber());

            if ($latestParkingSlip && $latestParkingSlip->isOngoing()) {
                throw new ParkingSlipsStillActiveException('Vehicle has not exited the parking yet.');
            }

            $availableParkingSlots = $this->parkingSlotsService->getAllAvailable();

            if (!$availableParkingSlots->count()) {
                throw new ParkingSlotsException('No available parking slot at this time.');
            }

            $nearestParkingSlot = $availableParkingSlots->getNearestForVehicleType($request->getEntryPoint(), $vehicle->getType());

            if (!$nearestParkingSlot) {
                throw new ParkingSlotsException('No available parking slot for vehicle type.');
            }

            $this->parkingSlipsService->process($nearestParkingSlot->getId(), $vehicle->getPlateNumber(), $latestParkingSlip);

            $this->parkingSlotsService->updateAsUnavailable($nearestParkingSlot);

            $response = new ParkResponse($nearestParkingSlot);
        } catch (\Exception $e) {
            $response = new ParkResponse();
            $response->setErrorCode($e->getCode());
            $response->setErrorMessage($e->getMessage());
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return mixed|void
     * @throws ParkingActionInvalidRequestException
     * @throws ParkingFunctionsMissingArgumentException
     * @throws ParkingFunctionsParkColorInvalidException
     * @throws ParkingFunctionsParkPlateNumberInvalidException
     * @throws ParkingFunctionsParkVehicleTypeInvalidException
     * @throws ParkingMapInvalidEntryPointException
     */
    public function validate($request)
    {
        if (!$request instanceof Request) {
            throw new ParkingActionInvalidRequestException('Invalid request. Should be an instance of Request.');
        }

        if (!$request->getEntryPoint() || !$request->getPlateNumber() || !$request->getType()) {
            throw new ParkingFunctionsMissingArgumentException('Please enter all required parameters');
        }

        if (!$this->parkingMapService->isValidEntryPoint($request->getEntryPoint())) {
            throw new ParkingMapInvalidEntryPointException('Invalid entry point passed');
        }

        if (!$this->vehiclesService->isValidPlateNumber($request->getPlateNumber())) {
            throw new ParkingFunctionsParkPlateNumberInvalidException('Please enter a valid plate number (alphanumeric)');
        }

        if (!$this->vehiclesService->isValidType($request->getType())) {
            throw new ParkingFunctionsParkVehicleTypeInvalidException('Please enter a valid vehicle type');
        }

        if (!$this->vehiclesService->isValidColor($request->getColor())) {
            throw new ParkingFunctionsParkColorInvalidException('Please enter a valid color');
        }
    }
}