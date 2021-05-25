<?php

namespace Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingSlots;

use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlot;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsDao;
use Database;

/**
 * Class ParkingSlotsDaoImpl
 * @package Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingSlots
 */
class ParkingSlotsDaoImpl implements ParkingSlotsDao
{
    private $db;
    private $tableName = 'parkingSlots';

    /**
     * ParkingSlotsDaoImpl constructor.
     */
    public function __construct()
    {
        $this->db = Database::connection();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        $queryStatement = "SELECT * FROM {$this->tableName} WHERE id = ?";
        $queryParams = [$id];

        return $this->db->executeQuery($queryStatement, $queryParams)->fetch();
    }

    /**
     * @return mixed
     */
    public function getAll()
    {
        $queryStatement = "SELECT * FROM {$this->tableName}";
        $queryParams = [];

        return $this->db->executeQuery($queryStatement, $queryParams)->fetchAll();
    }

    /**
     * @return mixed
     */
    public function getAllAvailable()
    {
        $queryStatement = "SELECT * FROM {$this->tableName} WHERE isAvailable = ?";
        $queryParams = [true];

        return $this->db->executeQuery($queryStatement, $queryParams)->fetchAll();
    }

    /**
     * @param ParkingSlot $parkingSlot
     * @return mixed|void
     */
    public function add($parkingSlot)
    {
        $queryStatement = "INSERT INTO {$this->tableName} (type, distancePoints) VALUES (?, ?)";
        $queryParams = [
            $parkingSlot->getType(),
            serialize($parkingSlot->getDistancePoints())
        ];

        $this->db->execute($queryStatement, $queryParams);
    }

    /**
     * Delete all rows from table
     */
    public function deleteAll()
    {
        $queryStatement = "DELETE FROM {$this->tableName}";

        $this->db->execute($queryStatement);
    }

    /**
     * @param ParkingSlot $parkingSlot
     * @return mixed|void
     */
    public function updateAvailability($parkingSlot)
    {
        $queryStatement = "UPDATE {$this->tableName} SET isAvailable = ? WHERE id = ?";
        $queryParams = [$parkingSlot->getIsAvailable(), $parkingSlot->getId()];

        $this->db->execute($queryStatement, $queryParams);
    }

    public function getParkingSlotsDetail()
    {
        $queryStatement = "SELECT slo.id, slo.type, slo.distancePoints, slo.isAvailable, v.plateNumber, v.type AS vehicleType, v.color, sli.id AS parkingSlipId, sli.entryTime
                            FROM parkingSlots slo 
                            LEFT JOIN vehicles v ON v.plateNumber = (
                                SELECT sli.plateNumber
                                FROM parkingSlip sli
                                WHERE sli.parkingSlotId = slo.id
                                AND sli.exitTime IS NULL
                                ORDER BY sli.entryTime DESC
                                LIMIT 1
                            )
                            LEFT JOIN parkingSlip sli ON sli.id = (
                                SELECT sli.id
                                FROM parkingSlip sli
                                WHERE sli.plateNumber = v.plateNumber
                                AND sli.exitTime IS NULL
                                ORDER BY sli.entryTime DESC
                                LIMIT 1
                            )";
        $queryParams = [];

        return $this->db->executeQuery($queryStatement, $queryParams)->fetchAll();
    }
}