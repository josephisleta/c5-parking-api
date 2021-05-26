<?php

namespace Concrete\Package\ParkingApi\Src\Application\Parking\Responses;

/**
 * Class GetInfoResponse
 * @package Concrete\Package\ParkingApi\Src\Application\Parking\Responses
 */
class GetInfoResponse extends AbstractResponse
{
    protected $errorMessage;
    protected $errorCode;

    protected $exitOrExitQuantity;
    /** @var array $parkingSlots */
    protected $parkingSlotsArray;

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
     * @return array
     */
    public function getParkingSlotsArray()
    {
        return $this->parkingSlotsArray;
    }

    /**
     * @param array $parkingSlotsArray
     */
    public function setParkingSlotsArray($parkingSlotsArray)
    {
        $this->parkingSlotsArray = $parkingSlotsArray;
    }

    /**
     * @return array
     */
    public function getSuccessBody()
    {
        return [
            'entryOrExitQuantity' => $this->getExitOrExitQuantity(),
            'parkingSlots' => $this->getParkingSlotsArray(),
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