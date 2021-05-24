<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingSlips;

use Concrete\Package\ParkingApi\Src\Domain\ParkingFee\ParkingFeeService;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlipsStillActiveException;
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
    public function __construct(ParkingSlipsDao $parkingSlipsDao)
    {
        $this->parkingSlipsDao = $parkingSlipsDao;
    }

    /**
     * @param $parkingSlotId
     * @param $plateNumber
     * @param ParkingSlip|null $latestParkingSlip
     */
    public function process($parkingSlotId, $plateNumber, $latestParkingSlip = null)
    {
        if ($latestParkingSlip) {
            if ($this->isReturningVehicleByParkingSlip($latestParkingSlip)) {
                $latestParkingSlip->setExitTime(null);
                $latestParkingSlip->setFee(null);
                $this->parkingSlipsDao->update($latestParkingSlip);
                return;
            }
        }

        $parkingSlip = new ParkingSlip();
        $parkingSlip->setParkingSlotId($parkingSlotId);
        $parkingSlip->setPlateNumber($plateNumber);

        $this->parkingSlipsDao->add($parkingSlip);
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
    public function exitParkingSlip($parkingSlip, $parkingSlotType)
    {
        $parkingFee = ParkingFeeService::build($parkingSlotType);

        $parkingSlip->setExitTime(date('Y-m-d H:i:s'));
        $parkingSlip->setFee($parkingFee->get($parkingSlip->getEntryTime(), $parkingSlip->getExitTime()));

        $this->parkingSlipsDao->update($parkingSlip);

        return $parkingSlip;
    }

    /**
     * @param $plateNumber
     * @return ParkingSlip|null
     */
    public function getLatestByPlateNumber($plateNumber)
    {
        $parkingSlip = $this->parkingSlipsDao->getLatestByPlateNumber($plateNumber);
        if ($parkingSlip) {
            return new ParkingSlip($parkingSlip);
        }

        return null;
    }

    /**
     * @param ParkingSlips $parkingSlips
     * @return bool
     */
    public function isLatestParkingSlipActive($parkingSlips)
    {
        if ($parkingSlips->count()) {
            $latestParkingSlip = $parkingSlips->getLatest();
            if ($latestParkingSlip && $latestParkingSlip->isOngoing()) {
                return true;
            }
        }

        return false;
    }
}