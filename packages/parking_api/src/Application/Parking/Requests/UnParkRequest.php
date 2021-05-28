<?php

namespace Concrete\Package\ParkingApi\Src\Application\Parking\Requests;

/**
 * Class UnPark
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingActions\Requests
 */
class UnParkRequest implements Request
{
    protected $plateNumber;

    /**
     * UnParkRequest constructor.
     * @param array $request
     */
    public function __construct($request)
    {
        $this->setPlateNumber($request['plateNumber']);
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

}