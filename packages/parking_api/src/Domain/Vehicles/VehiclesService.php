<?php

namespace Concrete\Package\ParkingApi\Src\Domain\Vehicles;

/**
 * Class VehiclesService
 * @package Concrete\Package\ParkingApi\Src\Domain\Vehicles
 */
class VehiclesService
{
    private $vehiclesDao;

    /**
     * VehiclesService constructor.
     * @param VehiclesDao $vehiclesDao
     */
    public function __construct(VehiclesDao $vehiclesDao)
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
}