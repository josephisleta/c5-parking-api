<?php

use Concrete\Package\ParkingApi\Src\Domain\ParkingFee\ParkingFee;
use Concrete\Package\ParkingApi\Src\Domain\ParkingFee\ParkingFeeService;
use PHPUnit\Framework\TestCase;

class SPTest extends TestCase
{
    /** @var ParkingFee $parkingFee */
    private $parkingFee;

    const PARKING_SLOT_TYPE = 'SP';

    public function setUp(): void
    {
        $this->parkingFee = ParkingFeeService::build(self::PARKING_SLOT_TYPE);
    }

    public function testClass()
    {
        $this->assertInstanceOf('Concrete\Package\ParkingApi\Src\Domain\ParkingFee\ParkingFee', $this->parkingFee);
        $this->assertInstanceOf('Concrete\Package\ParkingApi\Src\Domain\ParkingFee\Types\SP', $this->parkingFee);
    }

    public function testFlatRate()
    {
        $entryTime = date('Y-m-d H:i:s');
        $exitTime = date('Y-m-d H:i:s');
        $this->assertEquals(40, $this->parkingFee->get($entryTime, $exitTime));

        // 1 hr
        $entryTime = '2021-05-24 00:00:00';
        $exitTime = '2021-05-24 01:00:00';
        $this->assertEquals(40, $this->parkingFee->get($entryTime, $exitTime));

        // 2 hrs
        $entryTime = '2021-05-24 00:00:00';
        $exitTime = '2021-05-24 02:00:00';
        $this->assertEquals(40, $this->parkingFee->get($entryTime, $exitTime));

        // 3 hrs
        $entryTime = '2021-05-24 00:00:00';
        $exitTime = '2021-05-24 03:00:00';
        $this->assertEquals(40, $this->parkingFee->get($entryTime, $exitTime));
    }

    public function testMoreThan3Hrs()
    {
        // 4 hrs
        $entryTime = '2021-05-24 00:00:00';
        $exitTime = '2021-05-24 04:00:00';
        $this->assertEquals(60, $this->parkingFee->get($entryTime, $exitTime));

        // 5 hrs
        $entryTime = '2021-05-24 00:00:00';
        $exitTime = '2021-05-24 05:00:00';
        $this->assertEquals(80, $this->parkingFee->get($entryTime, $exitTime));

        // 6 hrs
        $entryTime = '2021-05-24 00:00:00';
        $exitTime = '2021-05-24 06:00:00';
        $this->assertEquals(100, $this->parkingFee->get($entryTime, $exitTime));

        // 6.4 hrs
        $entryTime = '2021-05-24 00:00:00';
        $exitTime = '2021-05-24 06:25:00';
        $this->assertEquals(100, $this->parkingFee->get($entryTime, $exitTime));

        // 6.5 hrs
        $entryTime = '2021-05-24 00:00:00';
        $exitTime = '2021-05-24 06:30:00';
        $this->assertEquals(120, $this->parkingFee->get($entryTime, $exitTime));
    }

    public function test24Hrs()
    {
        // 24 hrs
        $entryTime = '2021-05-24 00:00:00';
        $exitTime = '2021-05-25 00:00:00';
        $this->assertEquals(5000, $this->parkingFee->get($entryTime, $exitTime));
    }

    public function testMoreThan24Hrs()
    {
        // 25 hrs
        $entryTime = '2021-05-24 00:00:00';
        $exitTime = '2021-05-25 01:00:00';
        $this->assertEquals(5020, $this->parkingFee->get($entryTime, $exitTime));

        // 47 hrs
        $entryTime = '2021-05-24 00:00:00';
        $exitTime = '2021-05-25 23:00:00';
        $this->assertEquals(5460, $this->parkingFee->get($entryTime, $exitTime));

        // 48 hrs
        $entryTime = '2021-05-24 00:00:00';
        $exitTime = '2021-05-26 00:00:00';
        $this->assertEquals(10000, $this->parkingFee->get($entryTime, $exitTime));
    }
}