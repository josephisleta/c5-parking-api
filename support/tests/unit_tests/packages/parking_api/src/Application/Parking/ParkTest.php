<?php

use Concrete\Package\ParkingApi\Src\Application\Parking\Actions\ParkAction;
use Concrete\Package\ParkingApi\Src\Application\Parking\Requests\ParkRequest;
use PHPUnit\Framework\TestCase;

class ParkTest extends TestCase
{
    private $parkAction;

    const DAO_AVAILABLE_SLOT_SP_1 = [
        'id' => 1,
        'type' => 'SP',
        'distancePoints' => 'a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}', // 1 2 3
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const DAO_AVAILABLE_SLOT_SP_2 = [
        'id' => 2,
        'type' => 'SP',
        'distancePoints' => 'a:3:{i:0;i:2;i:1;i:3;i:2;i:4;}', // 2 3 4
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const DAO_AVAILABLE_SLOT_MP_1 = [
        'id' => 3,
        'type' => 'MP',
        'distancePoints' => 'a:3:{i:0;i:3;i:1;i:4;i:2;i:5;}', // 3 4 5
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const DAO_AVAILABLE_SLOT_MP_2 = [
        'id' => 4,
        'type' => 'MP',
        'distancePoints' => 'a:3:{i:0;i:4;i:1;i:5;i:2;i:6;}', // 4 5 6
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const DAO_AVAILABLE_SLOT_LP_1 = [
        'id' => 5,
        'type' => 'LP',
        'distancePoints' => 'a:3:{i:0;i:5;i:1;i:6;i:2;i:1;}', // 5 6 1
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const DAO_AVAILABLE_SLOT_LP_2 = [
        'id' => 6,
        'type' => 'LP',
        'distancePoints' => 'a:3:{i:0;i:6;i:1;i:1;i:2;i:2;}', // 6 1 2
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const DAO_AVAILABLE_SLOTS = [
        self::DAO_AVAILABLE_SLOT_SP_1,
        self::DAO_AVAILABLE_SLOT_SP_2,
        self::DAO_AVAILABLE_SLOT_MP_1,
        self::DAO_AVAILABLE_SLOT_MP_2,
        self::DAO_AVAILABLE_SLOT_LP_1,
        self::DAO_AVAILABLE_SLOT_LP_2
    ];

    const DAO_PARKING_SLIP_ONGOING_1 = [
        'id' => 1,
        'parkingSlotId' => 100,
        'plateNumber' => 'MOCK123',
        'entryTime' => '2021-05-23 00:00:00',
        'exitTime' => null,
        'fee' => null
    ];

    const DAO_UNAVAILABLE_SLOT_SP_1 = [
        'id' => 1,
        'type' => 'SP',
        'distancePoints' => 'a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}',
        'isAvailable' => 0,
        'plateNumber' => 'PARKED1',
        'parkingSlipId' => 1
    ];

    public function setUp(): void
    {
        $this->parkAction = new ParkAction(
            $this->getParkingMapDaoMock(),
            $this->getParkingSlotsDaoMock(self::DAO_AVAILABLE_SLOTS),
            $this->getVehiclesDaoMock(),
            $this->getParkingSlipsDaoMock()
        );
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @throws ReflectionException
     */
    private function getParkingMapDaoMock()
    {
        $parkingMapDaoMock = $this->getMockBuilder('Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingMap')
            ->setMethods(['getEntryOrExitQuantity'])
            ->getMock();
        $parkingMap = [
            'id' => 1,
            'entryOrExitQuantity' => 3,
            'dateAdded' => '2021-05-23 00:00:00'
        ];
        $parkingMapDaoMock->method('getEntryOrExitQuantity')->willReturn($parkingMap);

        return $parkingMapDaoMock;
    }

    /**
     * @param array|null $getAllAvailableData
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @throws ReflectionException
     */
    private function getParkingSlotsDaoMock($getAllAvailableData = null)
    {
        $parkingSlotsDaoMock = $this->getMockBuilder('Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingSlots')
            ->setMethods(['getAllAvailable', 'updateAvailability'])
            ->getMock();

        $parkingSlotsDaoMock->method('getAllAvailable')->willReturn($getAllAvailableData);

        return $parkingSlotsDaoMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @throws ReflectionException
     */
    private function getVehiclesDaoMock()
    {
        $vehiclesDaoMock = $this->getMockBuilder('Concrete\Package\ParkingApi\Src\Infrastructure\Dao\Vehicles')
            ->setMethods(['add', 'update', 'get'])
            ->getMock();

        return $vehiclesDaoMock;
    }

    /**
     * @param array|null $latestParkingSlip
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @throws ReflectionException
     */
    private function getParkingSlipsDaoMock($latestParkingSlip = null)
    {
        $parkingSlipsDaoMock = $this->getMockBuilder('Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingSlips')
            ->setMethods(['getLatestByPlateNumber', 'add', 'update'])
            ->getMock();
        $parkingSlipsDaoMock->method('getLatestByPlateNumber')->willReturn($latestParkingSlip);

        return $parkingSlipsDaoMock;
    }

    public function testInvalidRequest()
    {
        $invalidInputs = [
            null,
            [
                'entryPoint' => '',
                'plateNumber' => '',
                'type' => '',
                'color' => ''
            ]
        ];

        foreach ($invalidInputs as $invalidInput) {
            $request = new ParkRequest($invalidInput);
            $response = $this->parkAction->process($request);

            $expectedReponse = [
                'errorMessage' => 'Please enter all required parameters',
                'errorCode' => 221
            ];
            $this->assertEquals(json_encode($expectedReponse), $response->toJson());
        }

    }

    public function testInvalidEntryPoint()
    {
        $invalidEntryPoints = [
            'a', -1, 4, 5, 'QWE'
        ];

        foreach ($invalidEntryPoints as $invalidEntryPoint) {
            $input = [
                'entryPoint' => $invalidEntryPoint,
                'plateNumber' => 'MOCK123',
                'type' => 'S',
                'color' => ''
            ];
            $request = new ParkRequest($input);
            $response = $this->parkAction->process($request);

            $expectedReponse = [
                'errorMessage' => 'Invalid entry point passed',
                'errorCode' => 211
            ];
            $this->assertEquals(json_encode($expectedReponse), $response->toJson());
        }
    }

    public function testInvalidPlateNumber()
    {
        $invalidPlateNumbers = [
            -1, '$#@#$', 'LENGTHIS9', 'ASDG-123'
        ];

        foreach ($invalidPlateNumbers as $invalidPlateNumber) {
            $input = [
                'entryPoint' => 1,
                'plateNumber' => $invalidPlateNumber,
                'type' => 'S',
                'color' => ''
            ];
            $request = new ParkRequest($input);
            $response = $this->parkAction->process($request);

            $expectedReponse = [
                'errorMessage' => 'Please enter a valid plate number (alphanumeric 1-8 characters)',
                'errorCode' => 222
            ];
            $this->assertEquals(json_encode($expectedReponse), $response->toJson());
        }
    }

    public function testInvalidType()
    {
        $invalidTypes = [
            'QWE', 1, -3, 'AB', 'A'
        ];

        foreach ($invalidTypes as $invalidType) {
            $input = [
                'entryPoint' => 1,
                'plateNumber' => 'TEST123',
                'type' => $invalidType,
                'color' => ''
            ];
            $request = new ParkRequest($input);
            $response = $this->parkAction->process($request);

            $expectedReponse = [
                'errorMessage' => 'Please enter a valid vehicle type',
                'errorCode' => 223
            ];
            $this->assertEquals(json_encode($expectedReponse), $response->toJson());
        }
    }

    public function testInvalidColor()
    {
        $invalidColors = [
            1, 'QWGEUYQGWEYQWGEYUGQWEGYUGUQWG'
        ];

        foreach ($invalidColors as $invalidColor) {
            $input = [
                'entryPoint' => 1,
                'plateNumber' => 'TEST123',
                'type' => 'L',
                'color' => $invalidColor
            ];
            $request = new ParkRequest($input);
            $response = $this->parkAction->process($request);

            $expectedReponse = [
                'errorMessage' => 'Please enter a valid color',
                'errorCode' => 224
            ];
            $this->assertEquals(json_encode($expectedReponse), $response->toJson());
        }
    }

    public function testVehicleIsStillParked()
    {
        $validInput = [
            'entryPoint' => 1,
            'plateNumber' => 'TEST123',
            'type' => 'L',
            'color' => ''
        ];
        $request = new ParkRequest($validInput);
        $parkAction = new ParkAction(
            $this->getParkingMapDaoMock(),
            $this->getParkingSlotsDaoMock(),
            $this->getVehiclesDaoMock(),
            $this->getParkingSlipsDaoMock(self::DAO_PARKING_SLIP_ONGOING_1)
        );
        $response = $parkAction->process($request);

        $expectedReponse = [
            'errorMessage' => 'Vehicle has not exited the parking yet.',
            'errorCode' => 231
        ];
        $this->assertEquals(json_encode($expectedReponse), $response->toJson());
    }

    public function testNoAvailableParkingSlot()
    {
        $validInput = [
            'entryPoint' => 1,
            'plateNumber' => 'TEST123',
            'type' => 'L',
            'color' => ''
        ];
        $request = new ParkRequest($validInput);
        $parkAction = new ParkAction(
            $this->getParkingMapDaoMock(),
            $this->getParkingSlotsDaoMock(null),
            $this->getVehiclesDaoMock(),
            $this->getParkingSlipsDaoMock()
        );
        $response = $parkAction->process($request);
        $expectedReponse = [
            'errorMessage' => 'No available parking slot at this time.',
            'errorCode' => 232
        ];
        $this->assertEquals(json_encode($expectedReponse), $response->toJson());
    }

    public function testNoAvailableParkingSlotForVehicleTypeL()
    {
        $validInput = [
            'entryPoint' => 1,
            'plateNumber' => 'TEST123',
            'type' => 'L',
            'color' => ''
        ];

        $availableParkingSlots = [
            self::DAO_AVAILABLE_SLOT_SP_1,
            self::DAO_AVAILABLE_SLOT_MP_1
        ];

        $request = new ParkRequest($validInput);
        $parkAction = new ParkAction(
            $this->getParkingMapDaoMock(),
            $this->getParkingSlotsDaoMock($availableParkingSlots),
            $this->getVehiclesDaoMock(),
            $this->getParkingSlipsDaoMock()
        );
        $response = $parkAction->process($request);
        $expectedReponse = [
            'errorMessage' => 'No available parking slot for vehicle type L as this time.',
            'errorCode' => 232
        ];
        $this->assertEquals(json_encode($expectedReponse), $response->toJson());
    }

    public function testNoAvailableParkingSlotForVehicleTypeM()
    {
        $validInput = [
            'entryPoint' => 1,
            'plateNumber' => 'TEST123',
            'type' => 'M',
            'color' => ''
        ];

        $availableParkingSlots = [
            self::DAO_AVAILABLE_SLOT_SP_1,
            self::DAO_AVAILABLE_SLOT_SP_2
        ];

        $request = new ParkRequest($validInput);
        $parkAction = new ParkAction(
            $this->getParkingMapDaoMock(),
            $this->getParkingSlotsDaoMock($availableParkingSlots),
            $this->getVehiclesDaoMock(),
            $this->getParkingSlipsDaoMock()
        );
        $response = $parkAction->process($request);
        $expectedReponse = [
            'errorMessage' => 'No available parking slot for vehicle type M as this time.',
            'errorCode' => 232
        ];
        $this->assertEquals(json_encode($expectedReponse), $response->toJson());
    }

    public function testSuccessfulParkForVehicleTypeL()
    {
        $validInput = [
            'entryPoint' => 1,
            'plateNumber' => 'TESTL123',
            'type' => 'L',
            'color' => ''
        ];

        $request = new ParkRequest($validInput);
        $parkAction = new ParkAction(
            $this->getParkingMapDaoMock(),
            $this->getParkingSlotsDaoMock(self::DAO_AVAILABLE_SLOTS),
            $this->getVehiclesDaoMock(),
            $this->getParkingSlipsDaoMock()
        );
        $response = $parkAction->process($request);
        $expectedReponse = [
            'parkingSlotId' => 5,
            'status' => 200
        ];
        $this->assertEquals(json_encode($expectedReponse), $response->toJson());
    }

    public function testSuccessfulParkForVehicleTypeM()
    {
        $validInput = [
            'entryPoint' => 1,
            'plateNumber' => 'TESTM123',
            'type' => 'M',
            'color' => ''
        ];

        $request = new ParkRequest($validInput);
        $parkAction = new ParkAction(
            $this->getParkingMapDaoMock(),
            $this->getParkingSlotsDaoMock(self::DAO_AVAILABLE_SLOTS),
            $this->getVehiclesDaoMock(),
            $this->getParkingSlipsDaoMock()
        );
        $response = $parkAction->process($request);
        $expectedReponse = [
            'parkingSlotId' => 3,
            'status' => 200
        ];
        $this->assertEquals(json_encode($expectedReponse), $response->toJson());
    }

    public function testSuccessfulParkForVehicleTypeS()
    {
        $validInput = [
            'entryPoint' => 1,
            'plateNumber' => 'TESTS123',
            'type' => 'S',
            'color' => ''
        ];

        $request = new ParkRequest($validInput);
        $parkAction = new ParkAction(
            $this->getParkingMapDaoMock(),
            $this->getParkingSlotsDaoMock(self::DAO_AVAILABLE_SLOTS),
            $this->getVehiclesDaoMock(),
            $this->getParkingSlipsDaoMock()
        );
        $response = $parkAction->process($request);
        $expectedReponse = [
            'parkingSlotId' => 1,
            'status' => 200
        ];
        $this->assertEquals(json_encode($expectedReponse), $response->toJson());
    }
}