<?php

namespace Concrete\Package\ParkingApi\Src\Domain\Vehicles;

/**
 * Interface VehiclesDao
 * @package Concrete\Package\ParkingApi\Src\Domain\Vehicles
 */
interface VehiclesDao
{
    /**
     * @param string $plateNumber
     * @return mixed
     */
    public function get($plateNumber);

    /**
     * @param Vehicle $vehicle
     * @return mixed
     */
    public function add($vehicle);

    /**
     * @param Vehicle $vehicle
     * @return mixed
     */
    public function update($vehicle);

}