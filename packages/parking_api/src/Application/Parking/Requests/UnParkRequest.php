<?php

namespace Concrete\Package\ParkingApi\Src\Application\Parking\Requests;

/**
 * Class UnPark
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingActions\Requests
 */
class UnParkRequest implements Request
{
    protected $parkingSlotId;

    /**
     * UnPark constructor.
     * @param $parkingSlotId
     */
    public function __construct($parkingSlotId)
    {
        $this->setParkingSlotId($parkingSlotId);
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

}