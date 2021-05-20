<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingFee\Types;

use Concrete\Package\ParkingApi\Src\Domain\ParkingFee\ParkingFee;

class SP extends ParkingFee
{
    protected $rateBase = 40;
    protected $rateHour = 20;
    protected $rate24Hours = 5000;
}