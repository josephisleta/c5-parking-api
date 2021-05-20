<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingFee\Types;

use Concrete\Package\ParkingApi\Src\Domain\ParkingFee\ParkingFee;

class LP extends ParkingFee
{
    protected $rateBase = 40;
    protected $rateHour = 100;
    protected $rate24Hours = 5000;
}