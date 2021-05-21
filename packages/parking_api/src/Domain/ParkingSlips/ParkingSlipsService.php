<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingSlips;

use Concrete\Package\ParkingApi\Src\Dao\ParkingSlips\ParkingSlipDaoImpl;
use Concrete\Package\ParkingApi\Src\Domain\ParkingFee\ParkingFeeService;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlotTypeInvalidException;

/**
 * Class ParkingSlipsService
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingSlips
 */
class ParkingSlipsService
{
    private $parkingSlipsDao;

    /**
     * ParkingSlipsService constructor.
     */
    public function __construct()
    {
        $this->parkingSlipsDao = new ParkingSlipDaoImpl();
    }

    /**
     * @param $parkingSlotId
     * @param $plateNumber
     */
    public function add($parkingSlotId, $plateNumber)
    {
        $parkingSlip = new ParkingSlip();
        $parkingSlip->setParkingSlotId($parkingSlotId);
        $parkingSlip->setPlateNumber($plateNumber);

        $this->parkingSlipsDao->add($parkingSlip);
    }

    /**
     * @param string $plateNumber
     * @return ParkingSlips
     */
    public function getByPlateNumber($plateNumber)
    {
        return new ParkingSlips($this->parkingSlipsDao->getByPlateNumber($plateNumber));
    }

    /**
     * @param $parkingSlotId
     * @return ParkingSlips
     */
    public function getByParkingSlotId($parkingSlotId)
    {
        return new ParkingSlips($this->parkingSlipsDao->getByParkingSlotId($parkingSlotId));
    }

    /**
     * @param ParkingSlip $parkingSlip
     * @param $parkingSlotType
     * @return ParkingSlip
     * @throws ParkingSlotTypeInvalidException
     */
    public function updateParkingSlip($parkingSlip, $parkingSlotType)
    {
        $parkingSlip->setExitTime(date('Y-m-d H:i:s'));

        $parkingFee = ParkingFeeService::build($parkingSlip->getEntryTime(), $parkingSlip->getExitTime(), $parkingSlotType);

        $parkingSlip->setFee($parkingFee->get());

        $this->parkingSlipsDao->update($parkingSlip);

        return $parkingSlip;
    }
}