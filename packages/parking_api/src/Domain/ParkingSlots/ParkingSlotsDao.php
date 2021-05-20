<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingSlots;

/**
 * Interface ParkingSlotsDao
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingSlots
 */
interface ParkingSlotsDao
{
    /**
     * @return mixed
     */
    public function getAll();

    /**
     * @param string $type
     * @return mixed
     */
    public function getAllByType($type);

    /**
     * @param ParkingSlot $parkingSlot
     * @return mixed
     */
    public function add($parkingSlot);

    /**
     * @return void
     */
    public function deleteAll();

    /**
     * @param $id
     * @param bool $isAvailable
     * @return mixed
     */
    public function updateAvailability($id, $isAvailable);
}