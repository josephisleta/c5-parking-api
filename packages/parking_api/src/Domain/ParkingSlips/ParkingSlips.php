<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingSlips;

/**
 * Class ParkingSlips
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingSlips
 */
class ParkingSlips
{
    private $data = [];

    /**
     * ParkingSlips constructor.
     * @param $data
     */
    public function __construct($data)
    {
        foreach ($data as $slip) {
            $this->data[] = new ParkingSlip($slip);
        }
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->data;
    }

    /**
     * @return ParkingSlip|mixed|null
     */
    public function getLatest()
    {
        $latestParkingSlip = null;

        /** @var ParkingSlip $parkingSlip */
        foreach ($this->data as $parkingSlip) {
            if (!$latestParkingSlip) {
                $latestParkingSlip = $parkingSlip;
                continue;
            }

            if ($parkingSlip->getEntryTime() > $latestParkingSlip->getEntryTime()) {
                $latestParkingSlip = $parkingSlip;
            }
        }

        return $latestParkingSlip;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

}