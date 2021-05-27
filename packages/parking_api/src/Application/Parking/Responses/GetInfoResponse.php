<?php

namespace Concrete\Package\ParkingApi\Src\Application\Parking\Responses;

use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlots;

/**
 * Class GetInfoResponse
 * @package Concrete\Package\ParkingApi\Src\Application\Parking\Responses
 */
class GetInfoResponse extends AbstractResponse
{
    protected $errorMessage;
    protected $errorCode;

    protected $exitOrExitQuantity;
    /** @var ParkingSlots $parkingSlots */
    protected $parkingSlots;
    protected $entryPoint;

    /**
     * GetInfoResponse constructor.
     */
    public function __construct()
    {

    }

    /**
     * @return mixed
     */
    public function getExitOrExitQuantity()
    {
        return $this->exitOrExitQuantity;
    }

    /**
     * @param mixed $exitOrExitQuantity
     */
    public function setExitOrExitQuantity($exitOrExitQuantity)
    {
        $this->exitOrExitQuantity = $exitOrExitQuantity;
    }

    /**
     * @return ParkingSlots
     */
    public function getParkingSlots()
    {
        return $this->parkingSlots;
    }

    /**
     * @param ParkingSlots $parkingSlots
     */
    public function setParkingSlots($parkingSlots)
    {
        $this->parkingSlots = $parkingSlots;
    }

    /**
     * @return mixed
     */
    public function getEntryPoint()
    {
        return $this->entryPoint;
    }

    /**
     * @param $entryPoint
     */
    public function setEntryPoint($entryPoint)
    {
        $this->entryPoint = $entryPoint;
    }

    /**
     * @return array
     */
    public function getSuccessBody()
    {
        return [
            'entryOrExitQuantity' => $this->getExitOrExitQuantity(),
            'parkingSlots' => $this->getParkingSlots()->toArray($this->getEntryPoint()),
            'status' => $this->status
        ];
    }

    /**
     * @return array
     */
    public function getErrorBody()
    {
        return [
            'errorMessage' => $this->getErrorMessage(),
            'errorCode' => $this->getErrorCode()
        ];
    }
}