<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingFee\Types;

use Concrete\Package\ParkingApi\Src\Domain\ParkingFee\ParkingFee;

/**
 * Class LP
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingFee\Types
 */
class LP extends ParkingFee
{
    protected $baseRate = 40;
    protected $hourlyRate = 100;
    protected $dailyRate = 5000;
}