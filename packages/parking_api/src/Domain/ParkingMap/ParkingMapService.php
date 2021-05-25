<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingMap;

/**
 * Class ParkingMapService
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingMap
 */
class ParkingMapService
{
    private $parkingMapDao;

    /**
     * ParkingMapService constructor.
     * @param ParkingMapDao $parkingMapDao
     */
    public function __construct($parkingMapDao)
    {
        $this->parkingMapDao = $parkingMapDao;
    }

    /**
     * @return int
     */
    public function getEntryOrExitQuantity()
    {
        $parkingMap = new ParkingMap($this->parkingMapDao->getEntryOrExitQuantity());

        return $parkingMap->getEntryOrExitQuantity();
    }

    /**
     * @param int $quantity
     */
    public function saveEntryOrExitQuantity($quantity)
    {
        $this->parkingMapDao->saveEntryOrExitQuantity($quantity);
    }

    /**
     * @param $entryPoint
     * @return bool
     */
    public function isValidEntryPoint($entryPoint)
    {
        return ($entryPoint > 0 && $entryPoint <= $this->getEntryOrExitQuantity());
    }

    /**
     * @param $entryOrExitQuantity
     * @return bool
     */
    public function isValidEntryOrExitQuantityInput($entryOrExitQuantity)
    {
        return ($entryOrExitQuantity && is_int($entryOrExitQuantity) && $entryOrExitQuantity >= 3);
    }
}