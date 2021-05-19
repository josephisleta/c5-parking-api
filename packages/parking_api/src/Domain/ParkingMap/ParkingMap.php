<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingMap;

/**
 * Class ParkingMap
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingMap
 */
class ParkingMap
{
    private $id;
    private $entryOrExitQuantity;
    private $dateAdded;

    const ENTRY_OR_EXIT_QUANTITY_DEFAULT = 3;

    /**
     * ParkingMap constructor.
     * @param array $data
     */
    public function __construct($data)
    {
        $this->setId($data['id']);
        $this->setEntryOrExitQuantity($data['entryOrExitQuantity']);
        $this->setDateAdded($data['dateAdded']);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getEntryOrExitQuantity()
    {
        return $this->entryOrExitQuantity;
    }

    /**
     * @param int $entryOrExitQuantity
     */
    public function setEntryOrExitQuantity($entryOrExitQuantity)
    {
        $this->entryOrExitQuantity = $entryOrExitQuantity;
    }

    /**
     * @return mixed
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @param mixed $dateAdded
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;
    }
}