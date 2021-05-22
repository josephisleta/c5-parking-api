<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingMap;

use Concrete\Package\ParkingApi\Src\Dao\ParkingMap\ParkingMapDaoImpl;
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
     */
    public function __construct()
    {
        $this->parkingMapDao = new ParkingMapDaoImpl();
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
     * @param $entryPoint
     * @throws ParkingMapInvalidEntryPointException
     */
    public function checkIfValidEntryPoint($entryPoint)
    {
        $entryPoints = $this->getEntryOrExitQuantity();

        if ($entryPoint < 0 || $entryPoint > $entryPoints) {
            throw new ParkingMapInvalidEntryPointException('Invalid entry point passed');
        }
    }

    /**
     * @param int $quantity
     */
    public function saveEntryOrExitQuantity($quantity)
    {
        $this->parkingMapDao->saveEntryOrExitQuantity($quantity);
    }
}