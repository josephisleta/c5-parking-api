<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingFee\Types;

use Concrete\Package\ParkingApi\Src\Domain\ParkingFee\ParkingFee;

/**
 * Class MP
 * @package Concrete\Package\ParkingApi\Src\Domain\ParkingFee\Types
 */
class MP extends ParkingFee
{
    protected $baseRate = 40;
    protected $hourlyRate = 60;
    protected $dailyRate = 5000;
}