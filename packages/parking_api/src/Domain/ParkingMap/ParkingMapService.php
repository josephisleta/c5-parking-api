<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingMap;

use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingMapInvalidEntryPointException;

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
    public function __construct(ParkingMapDao $parkingMapDao)
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
     * @throws ParkingMapInvalidEntryPointException
     */
    public function validateEntryPoint($entryPoint)
    {
        $entryPoints = $this->getEntryOrExitQuantity();

        if ($entryPoint < 0 || $entryPoint > $entryPoints) {
            throw new ParkingMapInvalidEntryPointException('Invalid entry point passed');
        }
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