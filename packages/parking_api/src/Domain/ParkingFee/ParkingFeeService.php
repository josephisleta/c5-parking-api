<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingFee;

use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlotTypeInvalidException;

/**
 * Class ParkingFeeService
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingFee
 */
class ParkingFeeService
{
    /**
     * @param $entryTime
     * @param $exitTime
     * @param $parkingSlotType
     * @return ParkingFee
     * @throws ParkingSlotTypeInvalidException
     */
    public static function build($entryTime, $exitTime, $parkingSlotType)
    {
        if (!class_exists('Concrete\Package\ParkingApi\Src\Domain\ParkingFee\Types\\' . $parkingSlotType)) {
            throw new ParkingSlotTypeInvalidException('The parking slot type ' . $parkingSlotType . ' is invalid.');
        }

        $parkingFeeClass = 'Concrete\Package\ParkingApi\Src\Domain\ParkingFee\Types\\' . $parkingSlotType;
        return new $parkingFeeClass($entryTime, $exitTime);
    }
}