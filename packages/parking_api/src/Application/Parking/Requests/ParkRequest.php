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
     * Park constructor.
     * @param string $entryPoint
     * @param string $plateNumber
     * @param string $type
     * @param string $color
     */
    public function __construct($entryPoint, $plateNumber, $type, $color)
    {
        $this->setEntryPoint($entryPoint);
        $this->setPlateNumber($plateNumber);
        $this->setType($type);
        $this->setColor($color);
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