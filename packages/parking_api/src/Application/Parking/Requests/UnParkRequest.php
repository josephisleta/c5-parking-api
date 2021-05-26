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
     * UnParkRequest constructor.
     * @param array $request
     */
    public function __construct($request)
    {
        $this->setParkingSlotId($request['parkingSlotId']);
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