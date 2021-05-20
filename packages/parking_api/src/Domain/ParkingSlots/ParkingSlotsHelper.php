<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingSlots;

/**
 * Class ParkingSlotsHelper
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingSlots
 */
class ParkingSlotsHelper
{
    public function __construct()
    {

    }

    /**
     * @param ParkingSlots $parkingSlots
     * @param bool $isEdit
     * @return array
     */
    public function toRawArray($parkingSlots, $isEdit = false)
    {
        $data = [];

        /** @var ParkingSlot $parkingSlot */
        foreach ($parkingSlots->getAll() as $parkingSlot) {
            $data[] = [
                "id" => $parkingSlot->getId(),
                "type" => $parkingSlot->getType(),
                "distancePoints" => unserialize($parkingSlot->getDistancePoints()),
                "isAvailable" => $parkingSlot->getIsAvailable()
            ];
        }

        return $data;
    }
}