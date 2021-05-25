<?php

namespace Concrete\Package\ParkingApi\Src\Application\Parking\Responses;

use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlot;

/**
 * Class ParkResponse
 * @package Concrete\Package\ParkingApi\Src\Application\Parking\Responses
 */
class ParkResponse extends AbstractResponse
{
    /** @var ParkingSlot $parkingSlot */
    protected $parkingSlot;

    /**
     * ParkResponse constructor.
     * @param ParkingSlot|null $parkingSlot
     */
    public function __construct($parkingSlot = null)
    {
        if ($parkingSlot) {
            $this->setParkingSlot($parkingSlot);
        }
    }

    /**
     * @return ParkingSlot
     */
    public function getParkingSlot()
    {
        return $this->parkingSlot;
    }

    /**
     * @param ParkingSlot $parkingSlot
     */
    public function setParkingSlot($parkingSlot)
    {
        $this->parkingSlot = $parkingSlot;
    }

    /**
     * @return array
     */
    public function getSuccessBody()
    {
        return [
            'parkingSlotId' => $this->getParkingSlot()->getId(),
            'status' => $this->status
        ];
    }

    /**
     * @return array
     */
    public function getErrorBody()
    {
        return [
            'errorMessage' => $this->getErrorMessage(),
            'errorCode' => $this->getErrorCode()
        ];
    }
}