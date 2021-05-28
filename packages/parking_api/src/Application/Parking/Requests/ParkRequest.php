<?php

namespace Concrete\Package\ParkingApi\Src\Application\Parking\Requests;

/**
 * Class Park
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingActions\Requests
 */
class ParkRequest implements Request
{
    protected $entryPoint;
    protected $plateNumber;
    protected $type;
    protected $color;

    /**
     * ParkRequest constructor.
     * @param array $request
     */
    public function __construct($request)
    {
        $this->setEntryPoint($request['entryPoint']);
        $this->setPlateNumber($request['plateNumber']);
        $this->setType($request['type']);
        $this->setColor($request['color']);
    }

    /**
     * @return string
     */
    public function getEntryPoint()
    {
        return $this->entryPoint;
    }

    /**
     * @param string $entryPoint
     */
    public function setEntryPoint($entryPoint)
    {
        $this->entryPoint = $entryPoint;
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

}