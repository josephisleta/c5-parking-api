<?php

namespace Concrete\Package\ParkingApi\Src\Application\Parking\Actions;

use Concrete\Package\ParkingApi\Src\Application\Parking\Requests\UnParkRequest as Request;
use Concrete\Package\ParkingApi\Src\Application\Parking\Responses\UnParkResponse;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlipsDao;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlipsService;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsDao;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsService;
use Concrete\Package\ParkingApi\Src\Domain\Vehicles\VehiclesDao;
use Concrete\Package\ParkingApi\Src\Domain\Vehicles\VehiclesService;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingActionInvalidRequestException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingActionVehicleNotExistingException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingFunctionsMissingArgumentException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingFunctionsParkPlateNumberInvalidException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlipsAlreadyExitedException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlipsDoesNotExistException;

/**
 * Class UnPark
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingActions\Actions
 */
class UnParkAction implements Action
{
    private $parkingSlotsService;
    private $vehiclesService;
    private $parkingSlipsService;

    /** @var UnParkResponse $response */
    private $response;

    /**
     * UnParkAction constructor.
     * @param ParkingSlotsDao $parkingSlotsDao
     * @param VehiclesDao $vehiclesDao
     * @param ParkingSlipsDao $parkingSlipsDao
     */
    public function __construct($parkingSlotsDao, $vehiclesDao, $parkingSlipsDao)
    {
        $this->parkingSlotsService = new ParkingSlotsService($parkingSlotsDao);
        $this->vehiclesService = new VehiclesService($vehiclesDao);
        $this->parkingSlipsService = new ParkingSlipsService($parkingSlipsDao);

        $this->response = new UnParkResponse();
    }

    /**
     * @param Request $request
     * @return UnParkResponse
     */
    public function process($request)
    {
        try {
            $this->validate($request);

            $latestParkingSlip = $this->parkingSlipsService->getLatestByPlateNumber($request->getPlateNumber());

            if (!$latestParkingSlip) {
                throw new ParkingSlipsDoesNotExistException('Parking slip for plate number ' . $request->getPlateNumber() . ' does not exist.');
            }

            if (!$latestParkingSlip->isOngoing()) {
                throw new ParkingSlipsAlreadyExitedException('The vehicle has already left the parking lot.');
            }

            $parkingSlot = $this->parkingSlotsService->getById($latestParkingSlip->getParkingSlotId());

            $parkingSlip = $this->parkingSlipsService->exitParkingSlip($latestParkingSlip, $parkingSlot->getType());

            $this->parkingSlotsService->updateAsAvailable($parkingSlot);

            $this->response->setParkingSlip($parkingSlip);

        } catch (\Exception $e) {
            $this->response->setErrorCode($e->getCode());
            $this->response->setErrorMessage($e->getMessage());
        }

        return $this->response;
    }

    /**
     * @param Request $request
     * @return mixed|void
     * @throws ParkingActionInvalidRequestException
     * @throws ParkingActionVehicleNotExistingException
     * @throws ParkingFunctionsMissingArgumentException
     * @throws ParkingFunctionsParkPlateNumberInvalidException
     */
    public function validate($request)
    {
        if (!$request instanceof Request) {
            throw new ParkingActionInvalidRequestException('Invalid request. Should be an instance of Request.');
        }

        if (!$request->getPlateNumber()) {
            throw new ParkingFunctionsMissingArgumentException('Please enter the plate number.');
        }

        if (!$this->vehiclesService->isValidPlateNumber($request->getPlateNumber())) {
            throw new ParkingFunctionsParkPlateNumberInvalidException('Please enter a valid plate number (alphanumeric 1-8 characters)');
        }

        $vehicle = $this->vehiclesService->getByPlateNumber($request->getPlateNumber());

        if (!$vehicle) {
            throw new ParkingActionVehicleNotExistingException('The plate number does not exist in our system.');
        }
    }
}