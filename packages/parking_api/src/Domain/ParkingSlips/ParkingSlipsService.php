<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingSlips;

use Concrete\Package\ParkingApi\Src\Dao\ParkingSlips\ParkingSlipDaoImpl;
use Concrete\Package\ParkingApi\Src\Domain\ParkingFee\ParkingFeeService;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlipsException;
use Concrete\Package\ParkingApi\Src\Exceptions\Parking\ParkingSlotTypeInvalidException;

class ParkingSlipsService
{
    private $parkingSlipsDao;

    public function __construct()
    {
        $this->parkingSlipsDao = new ParkingSlipDaoImpl();
    }

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
        $daoResult = $this->parkingSlipsDao->getByPlateNumber($plateNumber);

        return new ParkingSlips($daoResult);
    }

    public function hasOngoingParkingSlip($plateNumber)
    {
        $parkingSlips = $this->getByPlateNumber($plateNumber);

        if ($parkingSlips) {
            $latestParkingSlip = $parkingSlips->getLatest();
            if ($latestParkingSlip && !$latestParkingSlip->getExitTime()) {
                throw new ParkingSlipsException('Vehicle has not exited the parking yet.');
            }
        }
    }

    /**
     * @param $parkingSlotId
     * @return ParkingSlips
     */
    public function getByParkingSlotId($parkingSlotId)
    {
        $daoResult = $this->parkingSlipsDao->getByParkingSlotId($parkingSlotId);

        return new ParkingSlips($daoResult);
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

        $parkingFeeService = new ParkingFeeService($parkingSlip->getEntryTime(), $parkingSlip->getExitTime());
        $parkingFee = $parkingFeeService->build($parkingSlotType);

        $parkingSlip->setFee($parkingFee->get());

        $this->parkingSlipsDao->update($parkingSlip);

        return $parkingSlip;
    }
}