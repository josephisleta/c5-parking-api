<?php

namespace Concrete\Package\ParkingApi\Src\Dao\Vehicles;

use Concrete\Package\ParkingApi\Src\Domain\Vehicles\Vehicle;
use Concrete\Package\ParkingApi\Src\Domain\Vehicles\VehiclesDao;
use Database;

/**
 * Class VehiclesDaoImpl
 * @package Concrete\Package\ParkingApi\Src\Dao\Vehicles
 */
class VehiclesDaoImpl implements VehiclesDao
{
    private $db;
    private $tableName = 'vehicles';

    /**
     * VehiclesDaoImpl constructor.
     */
    public function __construct()
    {
        $this->db = Database::connection();
    }

    /**
     * @param string $plateNumber
     * @return mixed
     */
    public function get($plateNumber)
    {
        $queryStatement = "SELECT * FROM {$this->tableName} WHERE plateNumber = ?";
        $queryParams = [$plateNumber];

        return $this->db->executeQuery($queryStatement, $queryParams)->fetch();
    }

    /**
     * @param Vehicle $vehicle
     */
    public function add($vehicle)
    {
        $queryStatement = "INSERT INTO {$this->tableName} (plateNumber, type, color) VALUES (?, ?, ?)";
        $queryParams = [
            $vehicle->getPlateNumber(),
            $vehicle->getType(),
            $vehicle->getColor()
        ];

        $this->db->execute($queryStatement, $queryParams);
    }

    /**
     * @param Vehicle $vehicle
     */
    public function update($vehicle)
    {
        $queryStatement = "UPDATE {$this->tableName} SET type = ?, color = ? WHERE plateNumber = ?";
        $queryParams = [
            $vehicle->getType(),
            $vehicle->getColor(),
            $vehicle->getPlateNumber()
        ];

        $this->db->execute($queryStatement, $queryParams);
    }
}