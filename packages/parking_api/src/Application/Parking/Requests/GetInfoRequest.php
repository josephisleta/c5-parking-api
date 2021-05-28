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
     * GetInfoRequest constructor.
     * @param array $request
     */
    public function __construct($request)
    {
        $this->setEntryPoint($request['entryPoint']);
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