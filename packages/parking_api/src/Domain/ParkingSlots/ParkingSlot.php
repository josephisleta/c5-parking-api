<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingSlots;

use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlip;
use Concrete\Package\ParkingApi\Src\Domain\Vehicles\Vehicle;

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

    private $vehicle;
    private $parkingSlip;

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

            if ($data['plateNumber']) {
                $vehicle = new Vehicle();
                $vehicle->setPlateNumber($data['plateNumber']);
                $vehicle->setType($data['vehicleType']);
                $vehicle->setColor($data['color']);
                $this->setVehicle($vehicle);
            }

            if ($data['parkingSlipId']) {
                $parkingSlip = new ParkingSlip();
                $parkingSlip->setId($data['parkingSlipId']);
                $parkingSlip->setPlateNumber($data['plateNumber']);
                $parkingSlip->setParkingSlotId($data['id']);
                $parkingSlip->setEntryTime($data['entryTime']);

                $this->setParkingSlip($parkingSlip);
            }
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
     * @return Vehicle|mixed
     */
    public function getVehicle()
    {
        return $this->vehicle;
    }

    /**
     * @param Vehicle $vehicle
     */
    public function setVehicle($vehicle)
    {
        $this->vehicle = $vehicle;
    }

    /**
     * @return ParkingSlip
     */
    public function getParkingSlip()
    {
        return $this->parkingSlip;
    }

    /**
     * @param ParkingSlip $parkingSlip
     */
    public function setParkingSlip($parkingSlip)
    {
        $this->parkingSlip = $parkingSlip;
    }

    /**
     * @return bool
     */
    public function hasValidType()
    {
        return isset($this->slotTypeToVehicleMapping[$this->type]);
    }

    /**
     * @param string $vehicleType
     * @return bool
     */
    public function isVehicleTypeAllowed($vehicleType)
    {
        return in_array($vehicleType, $this->slotTypeToVehicleMapping[$this->type]);
    }

    /**
     * @param bool $entryPoint
     * @return array
     */
    public function toArray($entryPoint = false)
    {
        $distancePoints = unserialize($this->getDistancePoints());

        $arr = [
            "id" => $this->getId(),
            "type" => $this->getType(),
            "distancePoints" => $entryPoint ? $distancePoints[$entryPoint - 1] : $distancePoints,
            "isAvailable" => $this->getIsAvailable(),
        ];

        if ($this->getVehicle()) {
            $arr['vehicle'] = $this->getVehicle()->toArray();
        }

        if ($this->getParkingSlip()) {
            $arr['parkingSlip'] = $this->getParkingSlip()->toArray();
        }

        return $arr;
    }

}