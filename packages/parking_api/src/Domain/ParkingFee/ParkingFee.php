<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingFee;

use Concrete\Package\ParkingApi\Src\Helpers\DatetimeHelper;

/**
 * Class ParkingFee
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingFee
 */
abstract class ParkingFee
{
    protected $baseRate;
    protected $hourlyRate;
    protected $dailyRate;

    /** @var DatetimeHelper $datetimeHelper */
    private $datetimeHelper;

    const HOURS_FOR_BASE_RATE = 3;

    /**
     * ParkingFee constructor.
     */
    public function __construct()
    {
        $this->datetimeHelper = new DatetimeHelper();
    }

    /**
     * @param string $entryTime
     * @param string $exitTime
     * @return float|int
     */
    public function getTotal($entryTime, $exitTime)
    {
        $hours = $this->getHours($entryTime, $exitTime);

        if ($this->isDurationWithinBaseRate($hours)) {
            return $this->baseRate;
        }

        if ($hours < 24) {
            return $this->baseRate + ($this->getExcessHrsAfterBase($hours) * $this->hourlyRate);
        }

        $total = $this->getDays($hours) * $this->dailyRate;

        $hoursRemainder = $hours % 24;
        if ($hoursRemainder) {
            $total += $hoursRemainder * $this->hourlyRate;
        }

        return $total;
    }

    /**
     * @param $hours
     * @return bool
     */
    private function isDurationWithinBaseRate($hours)
    {
        return $hours <= self::HOURS_FOR_BASE_RATE;
    }

    /**
     * @param string $entryTime
     * @param string $exitTime
     * @return float
     */
    private function getHours($entryTime, $exitTime)
    {
        $hours = $this->datetimeHelper->getHrsDiff($entryTime, $exitTime);
        return round($hours, 0, PHP_ROUND_HALF_UP);
    }

    /**
     * @param $hours
     * @return int
     */
    private function getExcessHrsAfterBase($hours)
    {
        return $hours - self::HOURS_FOR_BASE_RATE;
    }

    /**
     * @param $hours
     * @return float
     */
    private function getDays($hours)
    {
        return floor($hours / 24);
    }
}