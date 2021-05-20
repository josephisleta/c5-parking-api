<?php

namespace Concrete\Package\ParkingApi\Src\Dao\ParkingSlips;

use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlip;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlipsDao;
use Database;

/**
 * Class ParkingSlipDaoImpl
 * @package Concrete\Package\ParkingApi\Src\Dao\ParkingSlips
 */
class ParkingSlipDaoImpl implements ParkingSlipsDao
{
    private $db;
    private $tableName = 'parkingSlip';

    /**
     * ParkingSlipDaoImpl constructor.
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
     * @param $plateNumber
     * @return mixed
     */
    public function getByPlateNumber($plateNumber)
    {
        $queryStatement = "SELECT * FROM {$this->tableName} WHERE plateNumber = ?";
        $queryParams = [$plateNumber];

        return $this->db->executeQuery($queryStatement, $queryParams)->fetchAll();
    }

    /**
     * @param $parkingSlotId
     * @return mixed
     */
    public function getByParkingSlotId($parkingSlotId)
    {
        $queryStatement = "SELECT * FROM {$this->tableName} WHERE parkingSlotId = ?";
        $queryParams = [$parkingSlotId];

        return $this->db->executeQuery($queryStatement, $queryParams)->fetchAll();
    }

    /**
     * @param ParkingSlip $parkingSlip
     */
    public function add($parkingSlip)
    {
        $queryStatement = "INSERT INTO {$this->tableName} (parkingSlotId, plateNumber) VALUES (?, ?)";
        $queryParams = [
            $parkingSlip->getParkingSlotId(),
            $parkingSlip->getPlateNumber()
        ];

        $this->db->execute($queryStatement, $queryParams);
    }

    /**
     * @param ParkingSlip $parkingSlip
     */
    public function update($parkingSlip)
    {
        $queryStatement = "UPDATE {$this->tableName} SET exitTime = ?, fee = ? WHERE id = ?";
        $queryParams = [
            $parkingSlip->getExitTime(),
            $parkingSlip->getFee(),
            $parkingSlip->getId()
        ];

        $this->db->execute($queryStatement, $queryParams);
    }
}