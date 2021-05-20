<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingSlips;

/**
 * Class ParkingSlip
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingSlips
 */
class ParkingSlip
{
    private $id;
    private $parkingSlotId;
    private $plateNumber;
    private $entryTime;
    private $exitTime;
    private $fee;

    /**
     * ParkingSlip constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        if ($data) {
            $this->setId($data['id']);
            $this->setParkingSlotId($data['parkingSlotId']);
            $this->setPlateNumber($data['plateNumber']);
            $this->setEntryTime($data['entryTime']);
            $this->setExitTime($data['exitTime']);
            $this->setFee($data['fee']);
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getParkingSlotId()
    {
        return $this->parkingSlotId;
    }

    /**
     * @param mixed $parkingSlotId
     */
    public function setParkingSlotId($parkingSlotId)
    {
        $this->parkingSlotId = $parkingSlotId;
    }

    /**
     * @return string
     */
    public function getPlateNumber()
    {
        return $this->plateNumber;
    }

    /**
     * @param string $plateNumber
     */
    public function setPlateNumber($plateNumber)
    {
        $this->plateNumber = $plateNumber;
    }

    /**
     * @return mixed
     */
    public function getEntryTime()
    {
        return $this->entryTime;
    }

    /**
     * @param mixed $entryTime
     */
    public function setEntryTime($entryTime)
    {
        $this->entryTime = $entryTime;
    }

    /**
     * @return mixed
     */
    public function getExitTime()
    {
        return $this->exitTime;
    }

    /**
     * @param mixed $exitTime
     */
    public function setExitTime($exitTime)
    {
        $this->exitTime = $exitTime;
    }

    /**
     * @return mixed
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * @param mixed $fee
     */
    public function setFee($fee)
    {
        $this->fee = $fee;
    }

    /**
     * @return bool
     */
    public function isOngoing()
    {
        return !$this->exitTime;
    }

}