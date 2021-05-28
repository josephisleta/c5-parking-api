<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingSlips;

/**
 * Interface ParkingSlipsDao
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingSlips
 */
interface ParkingSlipsDao
{
    /**
     * @param $plateNumber
     * @return mixed
     */
    public function getLatestByPlateNumber($plateNumber);

    /**
     * @param $parkingSlotId
     * @return mixed
     */
    public function getByParkingSlotId($parkingSlotId);

    /**
     * @param $parkingSlip
     * @return mixed
     */
    public function add($parkingSlip);

    /**
     * @param $parkingSlip
     * @return mixed
     */
    public function update($parkingSlip);

}