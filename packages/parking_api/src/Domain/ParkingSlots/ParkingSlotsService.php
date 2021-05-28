<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingSlots;

/**
 * Class ParkingSlotsService
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingSlots
 */
class ParkingSlotsService
{
    private $parkingSlotsDao;

    const VALID_TYPES = ['SP', 'MP', 'LP'];

    /**
     * ParkingSlotsService constructor.
     * @param ParkingSlotsDao $parkingSlotsDao
     */
    public function __construct($parkingSlotsDao)
    {
        $this->parkingSlotsDao = $parkingSlotsDao;
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
     * @return ParkingSlots
     */
    public function getParkingSlots()
    {
        return new ParkingSlots($this->parkingSlotsDao->getAll());
    }

    /**
     * @return ParkingSlots
     */
    public function getAllAvailable()
    {
        return new ParkingSlots($this->parkingSlotsDao->getAllAvailable());
    }

    /**
     * @return ParkingSlots
     */
    public function getParkingSlotsWithDetails()
    {
        return new ParkingSlots($this->parkingSlotsDao->getParkingSlotsDetail());
    }

    /**
     * @param array $parkingSlots
     * @codeCoverageIgnore
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
     * @codeCoverageIgnore
     */
    public function updateAsAvailable($parkingSlot)
    {
        $parkingSlot->setIsAvailable(true);
        $this->parkingSlotsDao->updateAvailability($parkingSlot);
    }

    /**
     * @param ParkingSlot $parkingSlot
     * @codeCoverageIgnore
     */
    public function updateAsUnavailable($parkingSlot)
    {
        $parkingSlot->setIsAvailable(false);
        $this->parkingSlotsDao->updateAvailability($parkingSlot);
    }

    /**
     * @param $type
     * @return bool
     */
    public function isValidType($type)
    {
        return $type && in_array($type, self::VALID_TYPES);
    }

}