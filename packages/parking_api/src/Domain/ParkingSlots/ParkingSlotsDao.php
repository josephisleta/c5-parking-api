<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingSlots;

/**
 * Interface ParkingSlotsDao
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingSlots
 */
interface ParkingSlotsDao
{
    /**
     * @param $id
     * @return mixed
     */
    public function getById($id);

    /**
     * @return mixed
     */
    public function getAll();

    /**
     * @return mixed
     */
    public function getAllAvailable();

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

    /**
     * @return mixed
     */
    public function getParkingSlotsDetail();
}