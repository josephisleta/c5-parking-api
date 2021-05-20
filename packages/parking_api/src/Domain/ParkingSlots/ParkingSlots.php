<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingSlots;

/**
 * Class ParkingSlots
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingSlots
 */
class ParkingSlots
{
    /** @var array $data */
    private $data = [];

    /**
     * ParkingSlots constructor.
     * @param $data
     */
    public function __construct($data)
    {
        if ($data) {
            foreach ($data as $slot) {
                $this->data[] = new ParkingSlot($slot);
            }
        }
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->data;
    }

    /**
     * @param $entryPoint
     * @param $vehicleType
     * @return ParkingSlot|mixed|null
     */
    public function getNearestForVehicleType($entryPoint, $vehicleType)
    {
        $nearestSlot = null;

        /** @var ParkingSlot $slot */
        foreach ($this->data as $slot) {
            if (!$slot->isVehicleTypeAllowed($vehicleType)) {
                continue;
            }

            if (!$nearestSlot) {
                $nearestSlot = $slot;
                continue;
            }

            $distancePoints = unserialize($slot->getDistancePoints());
            $nearestDistancePoints = unserialize($nearestSlot->getDistancePoints());

            $index = $entryPoint - 1;
            if ($distancePoints[$index] < $nearestDistancePoints[$index]) {
                $nearestSlot = $slot;
            }
        }

        return $nearestSlot;
    }

}