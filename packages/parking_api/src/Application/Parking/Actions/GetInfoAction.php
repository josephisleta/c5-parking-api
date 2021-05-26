<?php

namespace Concrete\Package\ParkingApi\Src\Application\Parking\Actions;

use Concrete\Package\ParkingApi\Src\Application\Parking\Requests\GetInfoRequest as Request;
use Concrete\Package\ParkingApi\Src\Application\Parking\Responses\GetInfoResponse;
use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMapDao;
use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMapService;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsDao;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsService;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingActionInvalidRequestException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingMapInvalidEntryPointException;

/**
 * Class Park
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingActions
 */
class GetInfoAction implements Action
{
    private $parkingMapService;
    private $parkingSlotsService;

    /** @var GetInfoResponse $response */
    private $response;

    /**
     * GetInfo constructor.
     * @param ParkingMapDao $parkingMapDao
     * @param ParkingSlotsDao $parkingSlotsDao
     */
    public function __construct(ParkingMapDao $parkingMapDao, ParkingSlotsDao $parkingSlotsDao)
    {
        $this->parkingMapService = new ParkingMapService($parkingMapDao);
        $this->parkingSlotsService = new ParkingSlotsService($parkingSlotsDao);

        $this->response = new GetInfoResponse();
    }

    /**
     * @param Request $request
     * @return GetInfoResponse
     */
    public function process($request)
    {
        try {
            $this->validate($request);

            $entryPoint = $request->getEntryPoint();

            if ($entryPoint) {
                $parkingSlotsArray = $this->parkingSlotsService->getParkingSlotsWithDetails()->sortByEntryPoint($entryPoint)->toArray($entryPoint);
            } else {
                $parkingSlotsArray = $this->parkingSlotsService->getParkingSlotsWithDetails()->toArray();
            }

            $this->response->setExitOrExitQuantity($this->parkingMapService->getEntryOrExitQuantity());
            $this->response->setParkingSlotsArray($parkingSlotsArray);

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
     * @throws ParkingMapInvalidEntryPointException
     */
    public function validate($request)
    {
        if (!$request instanceof Request) {
            throw new ParkingActionInvalidRequestException('Invalid request. Should be an instance of Request.');
        }

        if ($request->getEntryPoint()) {
            if (!$this->parkingMapService->isValidEntryPoint($request->getEntryPoint())) {
                throw new ParkingMapInvalidEntryPointException('Invalid entry point passed');
            }
        }
    }
}