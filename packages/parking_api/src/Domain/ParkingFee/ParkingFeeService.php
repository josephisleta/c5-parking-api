<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingFee;

use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlotTypeInvalidException;

/**
 * Class ParkingFeeService
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingFee
 */
class ParkingFeeService
{
    private $entryTime;
    private $exitTime;

    /**
     * ParkingFeeService constructor.
     * @param $entryTime
     * @param $exitTime
     */
    public function __construct($entryTime, $exitTime)
    {
        $this->entryTime = $entryTime;
        $this->exitTime = $exitTime;
    }

    /**
     * @param $parkingSlotType
     * @return ParkingFee|mixed
     * @throws ParkingSlotTypeInvalidException
     */
    public function build($parkingSlotType)
    {
        if (!class_exists('Concrete\Package\ParkingApi\Src\Domain\ParkingFee\Types\\' . $parkingSlotType)) {
            throw new ParkingSlotTypeInvalidException('The parking slot type ' . $parkingSlotType . ' is invalid.');
        }

        $parkingFeeClass = 'Concrete\Package\ParkingApi\Src\Domain\ParkingFee\Types\\' . $parkingSlotType;
        return new $parkingFeeClass($this->entryTime, $this->exitTime);
    }
}