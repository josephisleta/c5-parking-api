<?php

namespace Concrete\Package\ParkingApi\Src\Domain\Vehicles;

/**
 * Class VehiclesService
 * @package Concrete\Package\ParkingApi\Src\Domain\Vehicles
 */
class VehiclesService
{
    private $vehiclesDao;

    const VALID_TYPES = ['S', 'M', 'L'];

    /**
     * VehiclesService constructor.
     * @param VehiclesDao $vehiclesDao
     */
    public function __construct($vehiclesDao)
    {
        $this->vehiclesDao = $vehiclesDao;
    }

    /**
     * @param string $plateNumber
     * @param string $type
     * @param string $color
     * @return Vehicle
     */
    public function add($plateNumber, $type, $color)
    {
        $vehicle = new Vehicle();
        $vehicle->setPlateNumber($plateNumber);
        $vehicle->setType($type);
        $vehicle->setColor($color);

        if ($this->vehiclesDao->get($plateNumber)) {
            $this->vehiclesDao->update($vehicle);
        } else {
            $this->vehiclesDao->add($vehicle);
        }

        return $vehicle;
    }

    /**
     * @param string $plateNumber
     * @return bool
     */
    public function isValidPlateNumber($plateNumber)
    {
        return $plateNumber && ctype_alnum($plateNumber);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isValidType($type)
    {
        return $type && in_array($type, self::VALID_TYPES);
    }

    /**
     * @param $color
     * @return bool
     */
    public function isValidColor($color)
    {
        if ($color) {
            return (bool) preg_match('/^[a-zA-Z ]*$/', $color);
        }

        return true;
    }
}