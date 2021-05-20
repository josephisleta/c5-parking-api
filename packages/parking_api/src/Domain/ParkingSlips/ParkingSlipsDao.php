<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingSlips;

interface ParkingSlipsDao
{
    public function getById($id);

    public function getByPlateNumber($plateNumber);

    public function getByParkingSlotId($parkingSlotId);

    public function add($parkingSlip);

    public function update($parkingSlip);

}