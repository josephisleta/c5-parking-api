<?php

namespace Concrete\Package\ParkingApi\Src\Helpers;

/**
 * Class DatetimeHelper
 * @package Concrete\Package\ParkingApi\Src\Helpers
 */
class DatetimeHelper
{
    /**
     * @param string $date1
     * @param string $date2
     * @return float|int
     */
    public function getHrsDiff($date1, $date2)
    {
        $date1 = strtotime($date1);
        $date2 = strtotime($date2);

        return abs($date1 - $date2) / 3600;
    }
}