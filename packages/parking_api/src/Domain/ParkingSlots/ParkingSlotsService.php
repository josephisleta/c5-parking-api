<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingSlots;

/**
 * Class ParkingSlotsService
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingSlots
 */
class ParkingSlotsService
{
    private $parkingSlotsDao;

    /**
     * ParkingSlotsService constructor.
     * @param ParkingSlotsDao $parkingSlotsDao
     */
    public function __construct(ParkingSlotsDao $parkingSlotsDao)
    {
        $this->parkingSlotsDao = $parkingSlotsDao;
    }

    /**
     * @return ParkingSlots
     */
    public function getParkingSlots()
    {
        return new ParkingSlots($this->parkingSlotsDao->getAll());
    }

    /**
     * @return ParkingSlots
     */
    public function getParkingSlotsWithDetails()
    {
        return new ParkingSlots($this->parkingSlotsDao->getParkingSlotsDetail());
    }

    /**
     * @return ParkingSlots
     */
    public function getAllAvailable()
    {
        return new ParkingSlots($this->parkingSlotsDao->getAllAvailable());
    }

    /**
     * @param $id
     * @return ParkingSlot
     */
    public function getById($id)
    {
        return new ParkingSlot($this->parkingSlotsDao->getById($id));
    }

    /**
     * @param array $parkingSlots
     */
    public function save($parkingSlots)
    {
        $this->parkingSlotsDao->deleteAll();

        foreach ($parkingSlots as $parkingSlot) {
            $parkingSlotObj = new ParkingSlot($parkingSlot);

            $this->parkingSlotsDao->add($parkingSlotObj);
        }
    }

    /**
     * @param ParkingSlot $parkingSlot
     */
    public function toggleAvailability($parkingSlot)
    {
        $this->parkingSlotsDao->updateAvailability($parkingSlot->getId(), !$parkingSlot->getIsAvailable());
    }

}