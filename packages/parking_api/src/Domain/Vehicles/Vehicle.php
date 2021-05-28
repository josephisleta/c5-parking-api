<?php

namespace Concrete\Package\ParkingApi\Src\Domain\Vehicles;

/**
 * Class Vehicle
 * @package Concrete\Package\ParkingApi\Src\Domain\Vehicles
 */
class Vehicle
{
    private $plateNumber;
    private $type;
    private $color;

    /**
     * Vehicle constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        if ($data) {
            $this->setPlateNumber($data['plateNumber']);
            $this->setType($data['type']);
            $this->setColor($data['color']);
        }
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

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'plateNumber' => $this->getPlateNumber(),
            'type' => $this->getType(),
            'color' => $this->getColor()
        ];
    }
}