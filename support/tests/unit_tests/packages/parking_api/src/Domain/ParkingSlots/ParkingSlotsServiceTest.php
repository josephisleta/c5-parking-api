<?php

use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlot;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlots as ParkingSlotsDomain;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsService;
use PHPUnit\Framework\TestCase;

class ParkingSlotsServiceTest extends TestCase
{
    private $parkingSlotsService;

    const DAO_AVAILABLE_SLOT_SP_1 = [
        'id' => 1,
        'type' => 'SP',
        'distancePoints' => 'a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}',
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const DAO_AVAILABLE_SLOT_SP_2 = [
        'id' => 2,
        'type' => 'SP',
        'distancePoints' => 'a:3:{i:0;i:2;i:1;i:3;i:2;i:4;}',
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const DAO_AVAILABLE_SLOT_MP_1 = [
        'id' => 3,
        'type' => 'MP',
        'distancePoints' => 'a:3:{i:0;i:3;i:1;i:4;i:2;i:5;}',
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const DAO_AVAILABLE_SLOT_MP_2 = [
        'id' => 4,
        'type' => 'MP',
        'distancePoints' => 'a:3:{i:0;i:4;i:1;i:5;i:2;i:6;}',
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const DAO_AVAILABLE_SLOT_LP_1 = [
        'id' => 5,
        'type' => 'LP',
        'distancePoints' => 'a:3:{i:0;i:5;i:1;i:6;i:2;i:1;}',
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const DAO_AVAILABLE_SLOT_LP_2 = [
        'id' => 6,
        'type' => 'LP',
        'distancePoints' => 'a:3:{i:0;i:6;i:1;i:1;i:2;i:2;}',
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const DAO_AVAILABLE_SLOTS_LIST = [
        self::DAO_AVAILABLE_SLOT_SP_1,
        self::DAO_AVAILABLE_SLOT_SP_2,
        self::DAO_AVAILABLE_SLOT_MP_1,
        self::DAO_AVAILABLE_SLOT_MP_2,
        self::DAO_AVAILABLE_SLOT_LP_1,
        self::DAO_AVAILABLE_SLOT_LP_2
    ];

    const DAO_UNAVAILABLE_SLOT_SP_1 = [
        'id' => 6,
        'type' => 'LP',
        'distancePoints' => 'a:3:{i:0;i:6;i:1;i:1;i:2;i:2;}',
        'isAvailable' => 1,
        'plateNumber' => 'MOCK123',
        'vehicleType' => 'M',
        'color' => 'white',
        'parkingSlipId' => 1,
        'entryTime' => '2021-05-23 00:00:00'
    ];

    public function setUp(): void
    {
        $parkingSlotsDaoMock = $this->getMockBuilder('Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingSlots')
            ->setMethods(['getById', 'getAll', 'getAllAvailable', 'getParkingSlotsDetail'])
            ->getMock();

        $parkingSlotsDaoMock->method('getById')->willReturn(self::DAO_AVAILABLE_SLOT_SP_1);
        $parkingSlotsDaoMock->method('getAll')->willReturn(self::DAO_AVAILABLE_SLOTS_LIST);
        $parkingSlotsDaoMock->method('getAllAvailable')->willReturn(self::DAO_AVAILABLE_SLOTS_LIST);
        $parkingSlotsDaoMock->method('getParkingSlotsDetail')->willReturn(self::DAO_AVAILABLE_SLOTS_LIST);

        $this->parkingSlotsService = new ParkingSlotsService($parkingSlotsDaoMock);
    }

    public function testGetById()
    {
        $expectedParkingSlot = new ParkingSlot([
            'id' => 1,
            'type' => 'SP',
            'distancePoints' => 'a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}',
            'isAvailable' => 1,
            'plateNumber' => null,
            'parkingSlipId' => null
        ]);
        $this->assertEquals($expectedParkingSlot, $this->parkingSlotsService->getById(1));
    }

    public function testGetParkingSlots()
    {
        $parkingSlots = $this->parkingSlotsService->getParkingSlots();

        $this->assertInstanceOf('Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlots', $parkingSlots);
        $this->assertEquals(6, $parkingSlots->count());
    }

    public function testGetNearestForVehicleTypeS()
    {
        $parkingSlots = $this->parkingSlotsService->getParkingSlots();

        $this->assertInstanceOf('Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlots', $parkingSlots);

        $expectedParkingSlot = new ParkingSlot(self::DAO_AVAILABLE_SLOT_SP_1);
        $this->assertEquals($expectedParkingSlot, $parkingSlots->getNearestForVehicleType(1, 'S'));

        $expectedParkingSlot = new ParkingSlot(self::DAO_AVAILABLE_SLOT_LP_2);
        $this->assertEquals($expectedParkingSlot, $parkingSlots->getNearestForVehicleType(2, 'S'));

        $expectedParkingSlot = new ParkingSlot(self::DAO_AVAILABLE_SLOT_LP_1);
        $this->assertEquals($expectedParkingSlot, $parkingSlots->getNearestForVehicleType(3, 'S'));
    }

    public function testGetNearestForVehicleTypeM()
    {
        $parkingSlots = $this->parkingSlotsService->getParkingSlots();

        $this->assertInstanceOf('Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlots', $parkingSlots);

        $expectedParkingSlot = new ParkingSlot(self::DAO_AVAILABLE_SLOT_MP_1);
        $this->assertEquals($expectedParkingSlot, $parkingSlots->getNearestForVehicleType(1, 'M'));

        $expectedParkingSlot = new ParkingSlot(self::DAO_AVAILABLE_SLOT_LP_2);
        $this->assertEquals($expectedParkingSlot, $parkingSlots->getNearestForVehicleType(2, 'M'));

        $expectedParkingSlot = new ParkingSlot(self::DAO_AVAILABLE_SLOT_LP_1);
        $this->assertEquals($expectedParkingSlot, $parkingSlots->getNearestForVehicleType(3, 'M'));
    }

    public function testGetNearestForVehicleTypeL()
    {
        $parkingSlots = $this->parkingSlotsService->getParkingSlots();

        $this->assertInstanceOf('Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlots', $parkingSlots);

        $expectedParkingSlot = new ParkingSlot(self::DAO_AVAILABLE_SLOT_LP_1);
        $this->assertEquals($expectedParkingSlot, $parkingSlots->getNearestForVehicleType(1, 'L'));

        $expectedParkingSlot = new ParkingSlot(self::DAO_AVAILABLE_SLOT_LP_2);
        $this->assertEquals($expectedParkingSlot, $parkingSlots->getNearestForVehicleType(2, 'L'));

        $expectedParkingSlot = new ParkingSlot(self::DAO_AVAILABLE_SLOT_LP_1);
        $this->assertEquals($expectedParkingSlot, $parkingSlots->getNearestForVehicleType(3, 'L'));
    }

    public function testGetNearestForVehicleTypeInvalid()
    {
        $parkingSlots = $this->parkingSlotsService->getParkingSlots();

        $this->assertInstanceOf('Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlots', $parkingSlots);

        $this->assertEquals(null, $parkingSlots->getNearestForVehicleType(1, 'X'));
    }

    public function testGetAllAvailable()
    {
        $parkingSlots = $this->parkingSlotsService->getAllAvailable();

        $this->assertInstanceOf('Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlots', $parkingSlots);

        $this->assertEquals(6, $parkingSlots->count());
    }

    public function testGetParkingSlotsWithDetails()
    {
        $parkingSlots = $this->parkingSlotsService->getParkingSlotsWithDetails();

        $this->assertInstanceOf('Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlots', $parkingSlots);

        $this->assertEquals(6, $parkingSlots->count());
    }

    public function testValidType()
    {
        $this->assertTrue($this->parkingSlotsService->isValidType('SP'));
        $this->assertTrue($this->parkingSlotsService->isValidType('MP'));
        $this->assertTrue($this->parkingSlotsService->isValidType('LP'));
    }

    public function testInvalidType()
    {
        $this->assertFalse($this->parkingSlotsService->isValidType(12));
        $this->assertFalse($this->parkingSlotsService->isValidType('QW'));
        $this->assertFalse($this->parkingSlotsService->isValidType('AB'));
    }

    public function testGetWithUnavailableSlot()
    {
        $parkingSlotsDaoMock = $this->getMockBuilder('Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingSlots')
            ->setMethods(['getParkingSlotsDetail'])
            ->getMock();
        $parkingSlotsDaoMock->method('getParkingSlotsDetail')->willReturn([self::DAO_UNAVAILABLE_SLOT_SP_1]);

        $parkingSlotsService = new ParkingSlotsService($parkingSlotsDaoMock);

        $expectedParkingSlots = new ParkingSlotsDomain([self::DAO_UNAVAILABLE_SLOT_SP_1]);
        $this->assertEquals($expectedParkingSlots, $parkingSlotsService->getParkingSlotsWithDetails());
    }
}