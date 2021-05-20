<?php

namespace Concrete\Package\ParkingApi\Src\Domain\ParkingFee\Types;

use Concrete\Package\ParkingApi\Src\Domain\ParkingFee\ParkingFee;

class MP extends ParkingFee
{
    protected $rateBase = 40;
    protected $rateHour = 60;
    protected $rate24Hours = 5000;
}