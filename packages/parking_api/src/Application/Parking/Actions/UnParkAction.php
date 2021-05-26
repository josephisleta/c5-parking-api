<?php

namespace Concrete\Package\ParkingApi\Src\Application\Parking\Actions;

use Concrete\Package\ParkingApi\Src\Application\Parking\Requests\UnParkRequest as Request;
use Concrete\Package\ParkingApi\Src\Application\Parking\Responses\UnParkResponse;
use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMapDao;
use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMapService;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlipsDao;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlipsService;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsDao;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsService;
use Concrete\Package\ParkingApi\Src\Domain\Vehicles\VehiclesDao;
use Concrete\Package\ParkingApi\Src\Domain\Vehicles\VehiclesService;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingActionInvalidRequestException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingFunctionsMissingArgumentException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlipsDoesNotExistException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlotAlreadyEmptyException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlotDoesNotExistException;

/**
 * Class UnPark
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingActions\Actions
 */
class UnParkAction implements Action
{
    private $parkingMapService;
    private $parkingSlotsService;
    private $vehiclesService;
    private $parkingSlipsService;

    /** @var UnParkResponse $response */
    private $response;

    /**
     * UnPark constructor.
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

            $latestParkingSlip = $this->parkingSlipsService->getByParkingSlotId($request->getParkingSlotId())->getLatest();

            if (!$latestParkingSlip) {
                throw new ParkingSlipsDoesNotExistException('Parking slip for parking slot id ' . $request->getParkingSlotId() . ' does not exist.');
            }

            $parkingSlot = $this->parkingSlotsService->getById($request->getParkingSlotId());

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
     * @throws ParkingFunctionsMissingArgumentException
     * @throws ParkingSlotAlreadyEmptyException
     * @throws ParkingSlotDoesNotExistException
     */
    public function validate($request)
    {
        if (!$request instanceof Request) {
            throw new ParkingActionInvalidRequestException('Invalid request. Should be an instance of Request.');
        }

        if (!$request->getParkingSlotId()) {
            throw new ParkingFunctionsMissingArgumentException('Please enter the parking slot id');
        }

        $parkingSlot = $this->parkingSlotsService->getById($request->getParkingSlotId());

        if (!$parkingSlot->getId()) {
            throw new ParkingSlotDoesNotExistException('Parking slot id ' . $request->getParkingSlotId() . ' does not exist.');
        }

        if ($parkingSlot->getIsAvailable()) {
            throw new ParkingSlotAlreadyEmptyException('Parking slot id ' . $request->getParkingSlotId() . ' is already empty.');
        }
    }
}