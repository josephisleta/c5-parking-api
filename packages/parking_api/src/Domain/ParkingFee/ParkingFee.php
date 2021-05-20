<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingFee;

abstract class ParkingFee
{
    protected $hours;

    protected $rateBase;
    protected $rateHour;
    protected $rate24Hours;

    /**
     * ParkingFee constructor.
     * @param $entryTime
     * @param $exitTime
     */
    public function __construct($entryTime, $exitTime)
    {
        $this->hours = $this->getHoursDiff(strtotime($entryTime), strtotime($exitTime));
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

    /**
     * @return int
     */
    public function get()
    {
        if ($this->hours <= 3) {
            return $this->rateBase;
        }

        if ($this->hours < 24) {
            $hoursAfterBase = $this->hours - 3;
            return $this->rateBase + ($this->rateHour * $hoursAfterBase);
        }

        $numOfFull24Hrs = floor($this->hours / 24);
        $total = $numOfFull24Hrs * $this->rate24Hours;

        $remainderHrs = $this->hours % 24;
        if ($remainderHrs) {
            $total += $remainderHrs * $this->rateHour;
        }

        return $total;
    }
}