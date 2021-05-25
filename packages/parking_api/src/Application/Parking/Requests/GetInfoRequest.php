<?php

namespace Concrete\Package\ParkingApi\Src\Application\Parking\Requests;

/**
 * Class GetInfo
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingActions\Requests
 */
class GetInfoRequest implements Request
{
    protected $entryPoint;

    /**
     * GetInfo constructor.
     * @param $entryPoint
     */
    public function __construct($entryPoint = '')
    {
        $this->setEntryPoint($entryPoint);
    }

    /**
     * @return mixed
     */
    public function getEntryPoint()
    {
        return $this->entryPoint;
    }

    /**
     * @param $entryPoint
     */
    public function setEntryPoint($entryPoint)
    {
        $this->entryPoint = $entryPoint;
    }

}