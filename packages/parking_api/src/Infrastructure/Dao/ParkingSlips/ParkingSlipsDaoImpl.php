<?php

namespace Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingSlips;

use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlip;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlipsDao;
use Database;

/**
 * Class ParkingSlipDaoImpl
 * @package Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingSlips
 */
class ParkingSlipsDaoImpl implements ParkingSlipsDao
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
     * @param $plateNumber
     * @return mixed
     */
    public function getLatestByPlateNumber($plateNumber)
    {
        $queryStatement = "SELECT * FROM {$this->tableName} WHERE plateNumber = ? ORDER BY entryTime DESC LIMIT 1";
        $queryParams = [$plateNumber];

        return $this->db->executeQuery($queryStatement, $queryParams)->fetch();
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
        $queryStatement = "UPDATE {$this->tableName} SET parkingSlotId = ?, exitTime = ?, fee = ? WHERE id = ?";
        $queryParams = [
            $parkingSlip->getParkingSlotId(),
            $parkingSlip->getExitTime(),
            $parkingSlip->getFee(),
            $parkingSlip->getId()
        ];

        $this->db->execute($queryStatement, $queryParams);
    }
}