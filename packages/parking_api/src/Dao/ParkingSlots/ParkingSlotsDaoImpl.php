<?php

namespace Concrete\Package\ParkingApi\Src\Dao\ParkingSlots;

use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlot;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsDao;
use Database;

/**
 * Class ParkingSlotsDaoImpl
 * @package Concrete\Package\ParkingApi\Src\Dao\ParkingSlots
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
     * @param string $type
     * @return mixed
     */
    public function getAllByType($type)
    {
        $queryStatement = "SELECT * FROM {$this->tableName} WHERE type = ?";
        $queryParams = [$type];

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
     * @param $id
     * @param bool $isAvailable
     */
    public function updateAvailability($id, $isAvailable)
    {
        $queryStatement = "UPDATE {$this->tableName} SET isAvailable = ? WHERE id = ?";
        $queryParams = [$isAvailable, $id];

        $this->db->execute($queryStatement, $queryParams);
    }
}