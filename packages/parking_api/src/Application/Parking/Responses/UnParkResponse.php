<?php

namespace Concrete\Package\ParkingApi\Src\Application\Parking\Responses;

use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlip;

/**
 * Class UnParkResponse
 * @package Concrete\Package\ParkingApi\Src\Application\Parking\Responses
 */
class UnParkResponse extends AbstractResponse
{
    /** @var ParkingSlip $parkingSlip */
    protected $parkingSlip;

    /**
     * UnParkResponse constructor.
     */
    public function __construct()
    {

    }

    /**
     * @return ParkingSlip
     */
    public function getParkingSlip()
    {
        return $this->parkingSlip;
    }

    /**
     * @param ParkingSlip $parkingSlip
     */
    public function setParkingSlip($parkingSlip)
    {
        $this->parkingSlip = $parkingSlip;
    }

    /**
     * @return array
     */
    public function getSuccessBody()
    {
        return [
            'parkingSlip' => $this->getParkingSlip()->toArray(),
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