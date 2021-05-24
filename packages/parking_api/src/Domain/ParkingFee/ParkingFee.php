<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingFee;

/**
 * Class ParkingFee
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingFee
 */
abstract class ParkingFee
{
    protected $rateBase;
    protected $rateHour;
    protected $rate24Hours;

    /**
     * @param $entryTime
     * @param $exitTime
     * @return float|int
     */
    public function get($entryTime, $exitTime)
    {
        $hours = $this->getHoursDiff(strtotime($entryTime), strtotime($exitTime));

        if ($hours <= 3) {
            return $this->rateBase;
        }

        if ($hours < 24) {
            $hoursAfterBase = $hours - 3;
            return $this->rateBase + ($this->rateHour * $hoursAfterBase);
        }

        $numOfFull24Hrs = floor($hours / 24);
        $total = $numOfFull24Hrs * $this->rate24Hours;

        $remainderHrs = $hours % 24;
        if ($remainderHrs) {
            $total += $remainderHrs * $this->rateHour;
        }

        return $total;
    }

    /**
     * @param $entryTime
     * @param $exitTime
     * @return float
     */
    private function getHoursDiff($entryTime, $exitTime)
    {
        $hours = abs($exitTime - $entryTime) / 3600;
        return round($hours, 0, PHP_ROUND_HALF_UP);
    }
}