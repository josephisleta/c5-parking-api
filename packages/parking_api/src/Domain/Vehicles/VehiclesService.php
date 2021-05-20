<?php

namespace Concrete\Package\ParkingApi\Src\Domain\Vehicles;

use Concrete\Package\ParkingApi\Src\Dao\Vehicles\VehiclesDaoImpl;

/**
 * Class VehiclesService
 * @package Concrete\Package\ParkingApi\Src\Domain\Vehicles
 */
class VehiclesService
{
    private $vehiclesDao;

    /**
     * VehiclesService constructor.
     */
    public function __construct()
    {
        $this->vehiclesDao = new VehiclesDaoImpl();
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