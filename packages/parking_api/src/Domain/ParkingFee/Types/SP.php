<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingFee\Types;

use Concrete\Package\ParkingApi\Src\Domain\ParkingFee\ParkingFee;

/**
 * Class SP
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingFee\Types
 */
class SP extends ParkingFee
{
    protected $baseRate = 40;
    protected $hourlyRate = 20;
    protected $dailyRate = 5000;
}