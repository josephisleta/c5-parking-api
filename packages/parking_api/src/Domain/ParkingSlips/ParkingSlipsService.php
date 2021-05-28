<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingSlips;

use Concrete\Package\ParkingApi\Src\Domain\ParkingFee\ParkingFeeService;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlotTypeInvalidException;
use Concrete\Package\ParkingApi\Src\Helpers\DatetimeHelper;

/**
 * Class ParkingSlipsService
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingSlips
 */
class ParkingSlipsService
{
    private $parkingSlipsDao;

    /**
     * ParkingSlipsService constructor.
     * @param ParkingSlipsDao $parkingSlipsDao
     */
    public function __construct($parkingSlipsDao)
    {
        $this->parkingSlipsDao = $parkingSlipsDao;
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
     * @param $plateNumber
     * @return ParkingSlip|null
     */
    public function getLatestByPlateNumber($plateNumber)
    {
        $parkingSlip = $this->parkingSlipsDao->getLatestByPlateNumber($plateNumber);
        return $parkingSlip ? new ParkingSlip($parkingSlip) : null;
    }

    /**
     * @param ParkingSlip $parkingSlip
     * @return bool
     */
    public function isReturningVehicleByParkingSlip($parkingSlip)
    {
        $exitTime = $parkingSlip->getExitTime();
        if ($exitTime) {
            $datetimeHelper = new DatetimeHelper();
            if ($datetimeHelper->getHrsDiff($exitTime, date('Y-m-d H:i:s')) <= 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $parkingSlotId
     * @param $plateNumber
     * @param ParkingSlip|null $latestParkingSlip
     */
    public function process($parkingSlotId, $plateNumber, $latestParkingSlip = null)
    {
        if ($latestParkingSlip && $this->isReturningVehicleByParkingSlip($latestParkingSlip)) {
            $latestParkingSlip->setParkingSlotId($parkingSlotId);
            $latestParkingSlip->setExitTime(null);
            $latestParkingSlip->setFee(null);
            $this->parkingSlipsDao->update($latestParkingSlip);
            return;
        }

        $parkingSlip = new ParkingSlip();
        $parkingSlip->setParkingSlotId($parkingSlotId);
        $parkingSlip->setPlateNumber($plateNumber);

        $this->parkingSlipsDao->add($parkingSlip);
    }

    /**
     * @param ParkingSlip $parkingSlip
     * @param $parkingSlotType
     * @return ParkingSlip
     * @throws ParkingSlotTypeInvalidException
     */
    public function exitParkingSlip($parkingSlip, $parkingSlotType)
    {
        $parkingFee = ParkingFeeService::build($parkingSlotType);

        $parkingSlip->setExitTime(date('Y-m-d H:i:s'));
        $parkingSlip->setFee($parkingFee->getTotal($parkingSlip->getEntryTime(), $parkingSlip->getExitTime()));

        $this->parkingSlipsDao->update($parkingSlip);

        return $parkingSlip;
    }
}