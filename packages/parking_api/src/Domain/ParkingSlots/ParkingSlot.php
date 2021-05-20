<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingSlots;

/**
 * Class ParkingSlot
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingSlots
 */
class ParkingSlot
{
    private $id;
    private $type;
    private $distancePoints;
    private $isAvailable;

    private $slotTypeToVehicleMapping = [
        'LP' => ['S', 'M', 'L'],
        'MP' => ['S', 'M'],
        'SP' => ['S']
    ];

    /**
     * ParkingSlot constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        if ($data) {
            $this->setId($data['id']);
            $this->setType($data['type']);
            $this->setDistancePoints($data['distancePoints']);
            $this->setIsAvailable($data['isAvailable']);
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return mixed
     */
    public function getDistancePoints()
    {
        return $this->distancePoints;
    }

    /**
     * @param $distancePoints
     */
    public function setDistancePoints($distancePoints)
    {
        $this->distancePoints = $distancePoints;
    }

    /**
     * @return mixed
     */
    public function getIsAvailable()
    {
        return $this->isAvailable;
    }

    /**
     * @param mixed $isAvailable
     */
    public function setIsAvailable($isAvailable)
    {
        $this->isAvailable = $isAvailable;
    }

    /**
     * @param string $vehicleType
     * @return bool
     */
    public function isVehicleTypeAllowed($vehicleType)
    {
        return in_array($vehicleType, $this->slotTypeToVehicleMapping[$this->type]);
    }

}