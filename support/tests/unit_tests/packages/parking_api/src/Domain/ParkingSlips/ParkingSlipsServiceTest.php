<?php

use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlip;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlipsService;
use PHPUnit\Framework\TestCase;

class ParkingSlipsServiceTest extends TestCase
{
    private $parkingSlipsService;

    const DAO_PARKING_SLIP_ONGOING_1 = [
        'id' => 1,
        'parkingSlotId' => 100,
        'plateNumber' => 'MOCKPLATE123',
        'entryTime' => '2021-05-23 00:00:00',
        'exitTime' => null,
        'fee' => null
    ];

    const DAO_PARKING_SLIP_ONGOING_2 = [
        'id' => 3,
        'parkingSlotId' => 101,
        'plateNumber' => 'MOCKPLATE999',
        'entryTime' => '2021-05-23 00:00:00',
        'exitTime' => null,
        'fee' => null
    ];

    const DAO_PARKING_SLIP_EXITED_1 = [
        'id' => 2,
        'parkingSlotId' => 101,
        'plateNumber' => 'MOCKPLATE321',
        'entryTime' => '2021-05-20 00:00:00',
        'exitTime' => '2021-05-20 01:00:00',
        'fee' => 40
    ];

    public function setUp(): void
    {
        $parkingSlipsDaoMock = $this->getMockBuilder('Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingMap')
            ->setMethods(['getLatestByPlateNumber', 'getByParkingSlotId'])
            ->getMock();

        $getLatestByPlateNumber = self::DAO_PARKING_SLIP_ONGOING_1;
        $parkingSlipsDaoMock->method('getLatestByPlateNumber')->willReturn($getLatestByPlateNumber);

        $getByParkingSlotId = [self::DAO_PARKING_SLIP_EXITED_1, self::DAO_PARKING_SLIP_ONGOING_2];
        $parkingSlipsDaoMock->method('getByParkingSlotId')->willReturn($getByParkingSlotId);

        $this->parkingSlipsService = new ParkingSlipsService($parkingSlipsDaoMock);
    }

    public function testGetByParkingSlotId()
    {
        $parkingSlips = $this->parkingSlipsService->getByParkingSlotId(101);

        $this->assertInstanceOf('Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlips', $parkingSlips);

        $this->assertEquals(2, $parkingSlips->count());

        $this->assertEquals([
            new ParkingSlip(self::DAO_PARKING_SLIP_EXITED_1),
            new ParkingSlip(self::DAO_PARKING_SLIP_ONGOING_2)
        ], $parkingSlips->getAll());

        $this->assertEquals(new ParkingSlip(self::DAO_PARKING_SLIP_ONGOING_2), $parkingSlips->getLatest());
    }

    public function testGetLatestByPlateNumber()
    {
        $latestParkingSlip = $this->parkingSlipsService->getLatestByPlateNumber('MOCKPLATE123');

        $this->assertInstanceOf('Concrete\Package\ParkingApi\Src\Domain\ParkingSlips\ParkingSlip', $latestParkingSlip);
    }

    public function testIsReturningVehicleByParkingSlip()
    {
        $this->assertTrue($this->parkingSlipsService->isReturningVehicleByParkingSlip(new ParkingSlip([
            'id' => 1,
            'parkingSlotId' => 1,
            'plateNumber' => 'TEST',
            'entryTime' => date('Y-m-d H:i:s'),
            'exitTime' => date('Y-m-d H:i:s'),
            'fee' => 40
        ])));

        $this->assertTrue($this->parkingSlipsService->isReturningVehicleByParkingSlip(new ParkingSlip([
            'id' => 1,
            'parkingSlotId' => 1,
            'plateNumber' => 'TEST',
            'entryTime' => date('Y-m-d H:i:s', strtotime('-15 minutes')),
            'exitTime' => date('Y-m-d H:i:s', strtotime('-15 minutes')),
            'fee' => 40
        ])));

        $this->assertTrue($this->parkingSlipsService->isReturningVehicleByParkingSlip(new ParkingSlip([
            'id' => 1,
            'parkingSlotId' => 1,
            'plateNumber' => 'TEST',
            'entryTime' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
            'exitTime' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
            'fee' => 40
        ])));

        $this->assertTrue($this->parkingSlipsService->isReturningVehicleByParkingSlip(new ParkingSlip([
            'id' => 1,
            'parkingSlotId' => 1,
            'plateNumber' => 'TEST',
            'entryTime' => date('Y-m-d H:i:s', strtotime('-60 minutes')),
            'exitTime' => date('Y-m-d H:i:s', strtotime('-60 minutes')),
            'fee' => 40
        ])));

    }

    public function testIsNotReturningVehicleByParkingSlip()
    {
        $this->assertFalse($this->parkingSlipsService->isReturningVehicleByParkingSlip(new ParkingSlip([
            'id' => 1,
            'parkingSlotId' => 1,
            'plateNumber' => 'TEST',
            'entryTime' => '2021-05-10 00:00:00',
            'exitTime' => '2021-05-10 01:00:00',
            'fee' => 40
        ])));

        $this->assertFalse($this->parkingSlipsService->isReturningVehicleByParkingSlip(new ParkingSlip([
            'id' => 1,
            'parkingSlotId' => 1,
            'plateNumber' => 'TEST',
            'entryTime' => date('Y-m-d H:i:s', strtotime('-61 minutes')),
            'exitTime' => date('Y-m-d H:i:s', strtotime('-61 minutes')),
            'fee' => 40
        ])));

        $this->assertFalse($this->parkingSlipsService->isReturningVehicleByParkingSlip(new ParkingSlip([
            'id' => 1,
            'parkingSlotId' => 1,
            'plateNumber' => 'TEST',
            'entryTime' => date('Y-m-d H:i:s', strtotime('-100 minutes')),
            'exitTime' => date('Y-m-d H:i:s', strtotime('-100 minutes')),
            'fee' => 40
        ])));
    }
}