<?php

use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlot;
use Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlotsService;
use PHPUnit\Framework\TestCase;

class ParkingSlotsServiceTest extends TestCase
{
    private $parkingSlotsService;

    const MOCK_AVAILABLE_LIST_DAO_1 = [
        'id' => 1,
        'type' => 'SP',
        'distancePoints' => 'a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}',
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const MOCK_AVAILABLE_LIST_DAO_2 = [
        'id' => 2,
        'type' => 'MP',
        'distancePoints' => 'a:3:{i:0;i:2;i:1;i:3;i:2;i:4;}',
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const MOCK_AVAILABLE_LIST_DAO_3 = [
        'id' => 3,
        'type' => 'LP',
        'distancePoints' => 'a:3:{i:0;i:3;i:1;i:4;i:2;i:5;}',
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const MOCK_AVAILABLE_LIST_DAO_4 = [
        'id' => 4,
        'type' => 'SP',
        'distancePoints' => 'a:3:{i:0;i:4;i:1;i:5;i:2;i:6;}',
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const MOCK_AVAILABLE_LIST_DAO_5 = [
        'id' => 5,
        'type' => 'MP',
        'distancePoints' => 'a:3:{i:0;i:5;i:1;i:6;i:2;i:1;}',
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const MOCK_AVAILABLE_LIST_DAO_6 = [
        'id' => 6,
        'type' => 'LP',
        'distancePoints' => 'a:3:{i:0;i:6;i:1;i:1;i:2;i:2;}',
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    public function setUp(): void
    {
        $parkingSlotsDaoMock = $this->getMockBuilder('Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingSlots')
            ->setMethods(['getById', 'getAll', 'getAllAvailable', 'getParkingSlotsDetail'])
            ->getMock();

        $getById = self::MOCK_AVAILABLE_LIST_DAO_1;
        $parkingSlotsDaoMock->method('getById')->willReturn($getById);

        $getAll = [
            self::MOCK_AVAILABLE_LIST_DAO_1,
            self::MOCK_AVAILABLE_LIST_DAO_2,
            self::MOCK_AVAILABLE_LIST_DAO_3,
            self::MOCK_AVAILABLE_LIST_DAO_4,
            self::MOCK_AVAILABLE_LIST_DAO_5,
            self::MOCK_AVAILABLE_LIST_DAO_6
        ];
        $parkingSlotsDaoMock->method('getAll')->willReturn($getAll);

        $getAllAvailable = [
            self::MOCK_AVAILABLE_LIST_DAO_1,
            self::MOCK_AVAILABLE_LIST_DAO_2,
            self::MOCK_AVAILABLE_LIST_DAO_3,
            self::MOCK_AVAILABLE_LIST_DAO_4,
            self::MOCK_AVAILABLE_LIST_DAO_5,
            self::MOCK_AVAILABLE_LIST_DAO_6
        ];
        $parkingSlotsDaoMock->method('getAllAvailable')->willReturn($getAllAvailable);

        $getParkingSlotsDetail = [
            self::MOCK_AVAILABLE_LIST_DAO_1,
            self::MOCK_AVAILABLE_LIST_DAO_2,
            self::MOCK_AVAILABLE_LIST_DAO_3,
            self::MOCK_AVAILABLE_LIST_DAO_4,
            self::MOCK_AVAILABLE_LIST_DAO_5,
            self::MOCK_AVAILABLE_LIST_DAO_6
        ];
        $parkingSlotsDaoMock->method('getParkingSlotsDetail')->willReturn($getParkingSlotsDetail);

        $this->parkingSlotsService = new ParkingSlotsService($parkingSlotsDaoMock);
    }

    public function testGetById()
    {
        $parkingSlot = new ParkingSlot([
            'id' => 1,
            'type' => 'SP',
            'distancePoints' => 'a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}',
            'isAvailable' => 1,
            'plateNumber' => null,
            'parkingSlipId' => null
        ]);
        $this->assertEquals($parkingSlot, $this->parkingSlotsService->getById(1));
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

        $this->assertEquals(new ParkingSlot(self::MOCK_AVAILABLE_LIST_DAO_1), $parkingSlots->getNearestForVehicleType(1, 'S'));

        $this->assertEquals(new ParkingSlot(self::MOCK_AVAILABLE_LIST_DAO_6), $parkingSlots->getNearestForVehicleType(2, 'S'));

        $this->assertEquals(new ParkingSlot(self::MOCK_AVAILABLE_LIST_DAO_5), $parkingSlots->getNearestForVehicleType(3, 'S'));
    }

    public function testGetNearestForVehicleTypeM()
    {
        $parkingSlots = $this->parkingSlotsService->getParkingSlots();

        $this->assertInstanceOf('Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlots', $parkingSlots);

        $this->assertEquals(new ParkingSlot(self::MOCK_AVAILABLE_LIST_DAO_2), $parkingSlots->getNearestForVehicleType(1, 'M'));

        $this->assertEquals(new ParkingSlot(self::MOCK_AVAILABLE_LIST_DAO_6), $parkingSlots->getNearestForVehicleType(2, 'M'));

        $this->assertEquals(new ParkingSlot(self::MOCK_AVAILABLE_LIST_DAO_5), $parkingSlots->getNearestForVehicleType(3, 'M'));
    }

    public function testGetNearestForVehicleTypeL()
    {
        $parkingSlots = $this->parkingSlotsService->getParkingSlots();

        $this->assertInstanceOf('Concrete\Package\ParkingApi\Src\Domain\ParkingSlots\ParkingSlots', $parkingSlots);

        $this->assertEquals(new ParkingSlot(self::MOCK_AVAILABLE_LIST_DAO_3), $parkingSlots->getNearestForVehicleType(1, 'L'));

        $this->assertEquals(new ParkingSlot(self::MOCK_AVAILABLE_LIST_DAO_6), $parkingSlots->getNearestForVehicleType(2, 'L'));

        $this->assertEquals(new ParkingSlot(self::MOCK_AVAILABLE_LIST_DAO_6), $parkingSlots->getNearestForVehicleType(3, 'L'));
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
}