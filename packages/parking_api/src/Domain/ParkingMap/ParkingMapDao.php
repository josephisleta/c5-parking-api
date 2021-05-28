<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingMap;

/**
 * Interface ParkingMapDao
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingMap
 */
interface ParkingMapDao
{
    /**
     * @param string $orderBy
     * @return mixed
     */
    public function getEntryOrExitQuantity($orderBy = 'DESC');

    /**
     * @param int $quantity
     * @return mixed
     */
    public function saveEntryOrExitQuantity($quantity);
}