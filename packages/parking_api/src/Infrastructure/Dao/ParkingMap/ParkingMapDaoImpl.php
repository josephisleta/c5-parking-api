<?php

namespace Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingMap;

use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMapDao;
use Database;

/**
 * Class ParkingMapDaoImpl
 * @package Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingMap
 * @codeCoverageIgnore
 */
class ParkingMapDaoImpl implements ParkingMapDao
{
    private $db;
    private $tableName = 'parkingMap';

    /**
     * ParkingMapDaoImpl constructor.
     */
    public function __construct()
    {
        $this->db = Database::connection();
    }

    /**
     * @param string $orderBy
     * @return mixed
     */
    public function getEntryOrExitQuantity($orderBy = 'DESC')
    {
        $queryStatement = "SELECT * FROM {$this->tableName} ORDER BY dateAdded {$orderBy} LIMIT 1";
        $queryParams = [];

        return $this->db->executeQuery($queryStatement, $queryParams)->fetch();
    }

    /**
     * @param int $quantity
     * @return mixed|void
     */
    public function saveEntryOrExitQuantity($quantity)
    {
        $queryStatement = "INSERT INTO {$this->tableName} (entryOrExitQuantity) VALUES (?)";
        $queryParams = [$quantity];

        $this->db->Execute($queryStatement, $queryParams);
    }
}