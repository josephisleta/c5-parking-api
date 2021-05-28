<?php

use Concrete\Package\ParkingApi\Src\Application\Parking\Actions\GetInfoAction;
use Concrete\Package\ParkingApi\Src\Application\Parking\Requests\GetInfoRequest;
use PHPUnit\Framework\TestCase;

class GetInfoTest extends TestCase
{
    const DAO_PARKING_MAP_3 = [
        'id' => 1,
        'entryOrExitQuantity' => 3,
        'dateAdded' => '2021-05-23 00:00:00'
    ];

    const DAO_AVAILABLE_SLOT_SP_1 = [
        'id' => 1,
        'type' => 'SP',
        'distancePoints' => 'a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}', // 1 2 3
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const DAO_AVAILABLE_SLOT_MP_1 = [
        'id' => 2,
        'type' => 'MP',
        'distancePoints' => 'a:3:{i:0;i:2;i:1;i:3;i:2;i:1;}', // 2 3 1
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const DAO_AVAILABLE_SLOT_LP_1 = [
        'id' => 3,
        'type' => 'LP',
        'distancePoints' => 'a:3:{i:0;i:3;i:1;i:1;i:2;i:2;}', // 3 1 2
        'isAvailable' => 1,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    const DAO_AVAILABLE_SLOTS = [
        self::DAO_AVAILABLE_SLOT_SP_1,
        self::DAO_AVAILABLE_SLOT_MP_1,
        self::DAO_AVAILABLE_SLOT_LP_1
    ];

    /**
     * @param array|null $parkingMapData
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @throws ReflectionException
     */
    private function getParkingMapDaoMock($parkingMapData = null)
    {
        $parkingMapDaoMock = $this->getMockBuilder('Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingMap')
            ->setMethods(['getEntryOrExitQuantity'])
            ->getMock();
        $parkingMapDaoMock->method('getEntryOrExitQuantity')->willReturn($parkingMapData);

        return $parkingMapDaoMock;
    }

    /**
     * @param array|null $parkingSlotsData
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @throws ReflectionException
     */
    private function getParkingSlotsDaoMock($parkingSlotsData = null)
    {
        $parkingSlotsDaoMock = $this->getMockBuilder('Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingSlots')
            ->setMethods(['getParkingSlotsDetail'])
            ->getMock();
        $parkingSlotsDaoMock->method('getParkingSlotsDetail')->willReturn($parkingSlotsData);

        return $parkingSlotsDaoMock;
    }

    public function testInvalidRequestNotObj()
    {
        $request = [];

        $getInfoAction = new GetInfoAction(
            $this->getParkingMapDaoMock(null),
            $this->getParkingSlotsDaoMock(null)
        );

        $response = $getInfoAction->process($request);

        $expectedResponse = [
            'errorMessage' => 'Invalid request. Should be an instance of Request.',
            'errorCode' => 500
        ];
        $this->assertEquals(json_encode($expectedResponse), $response->toJson());
    }

    public function testEmptyParkingLotData()
    {
        $input = null;
        $request = new GetInfoRequest($input);

        $getInfoAction = new GetInfoAction(
            $this->getParkingMapDaoMock(null),
            $this->getParkingSlotsDaoMock(null)
        );

        $response = $getInfoAction->process($request);

        $expectedResponse = [
            'entryOrExitQuantity' => null,
            'parkingSlots' => [],
            'status' => 200
        ];
        $this->assertEquals(json_encode($expectedResponse), $response->toJson());
    }

    public function testEmptyParkingSlotsData()
    {
        $input = null;
        $request = new GetInfoRequest($input);

        $getInfoAction = new GetInfoAction(
            $this->getParkingMapDaoMock(self::DAO_PARKING_MAP_3),
            $this->getParkingSlotsDaoMock(null)
        );

        $response = $getInfoAction->process($request);

        $expectedResponse = [
            'entryOrExitQuantity' => 3,
            'parkingSlots' => [],
            'status' => 200
        ];
        $this->assertEquals(json_encode($expectedResponse), $response->toJson());
    }

    public function testInvalidEntryPoint()
    {
        $invalidEntryPoints = [
            'a', -1, 4, 5, 'QWE'
        ];
        foreach ($invalidEntryPoints as $invalidEntryPoint) {
            $input = [
                'entryPoint' => $invalidEntryPoint
            ];
            $request = new GetInfoRequest($input);

            $getInfoAction = new GetInfoAction(
                $this->getParkingMapDaoMock(self::DAO_PARKING_MAP_3),
                $this->getParkingSlotsDaoMock(self::DAO_AVAILABLE_SLOTS)
            );

            $response = $getInfoAction->process($request);

            $expectedResponse = [
                'errorMessage' => 'Invalid entry point passed',
                'errorCode' => 211
            ];
            $this->assertEquals(json_encode($expectedResponse), $response->toJson());
        }
    }

    public function testNoEntryPointInput()
    {
        $input = null;
        $request = new GetInfoRequest($input);

        $getInfoAction = new GetInfoAction(
            $this->getParkingMapDaoMock(self::DAO_PARKING_MAP_3),
            $this->getParkingSlotsDaoMock(self::DAO_AVAILABLE_SLOTS)
        );

        $response = $getInfoAction->process($request);

        $expectedResponse = [
            'entryOrExitQuantity' => 3,
            'parkingSlots' => [
                [
                    'id' => 1,
                    'type' => 'SP',
                    'distancePoints' => [1, 2, 3],
                    'isAvailable' => 1
                ],
                [
                    'id' => 2,
                    'type' => 'MP',
                    'distancePoints' => [2, 3, 1],
                    'isAvailable' => 1
                ],
                [
                    'id' => 3,
                    'type' => 'LP',
                    'distancePoints' => [3, 1, 2],
                    'isAvailable' => 1
                ],
            ],
            'status' => 200
        ];
        $this->assertEquals(json_encode($expectedResponse), $response->toJson());
    }

    public function testEntryPoint1Input()
    {
        $input = [
            'entryPoint' => 1
        ];
        $request = new GetInfoRequest($input);

        $getInfoAction = new GetInfoAction(
            $this->getParkingMapDaoMock(self::DAO_PARKING_MAP_3),
            $this->getParkingSlotsDaoMock(self::DAO_AVAILABLE_SLOTS)
        );

        $response = $getInfoAction->process($request);

        $expectedResponse = [
            'entryOrExitQuantity' => 3,
            'parkingSlots' => [
                [
                    'id' => 1,
                    'type' => 'SP',
                    'distancePoints' => 1,
                    'isAvailable' => 1
                ],
                [
                    'id' => 2,
                    'type' => 'MP',
                    'distancePoints' => 2,
                    'isAvailable' => 1
                ],
                [
                    'id' => 3,
                    'type' => 'LP',
                    'distancePoints' => 3,
                    'isAvailable' => 1
                ],
            ],
            'status' => 200
        ];
        $this->assertEquals(json_encode($expectedResponse), $response->toJson());
    }

    public function testEntryPoint2Input()
    {
        $input = [
            'entryPoint' => 2
        ];
        $request = new GetInfoRequest($input);

        $getInfoAction = new GetInfoAction(
            $this->getParkingMapDaoMock(self::DAO_PARKING_MAP_3),
            $this->getParkingSlotsDaoMock(self::DAO_AVAILABLE_SLOTS)
        );

        $response = $getInfoAction->process($request);

        $expectedResponse = [
            'entryOrExitQuantity' => 3,
            'parkingSlots' => [
                [
                    'id' => 3,
                    'type' => 'LP',
                    'distancePoints' => 1,
                    'isAvailable' => 1
                ],
                [
                    'id' => 1,
                    'type' => 'SP',
                    'distancePoints' => 2,
                    'isAvailable' => 1
                ],
                [
                    'id' => 2,
                    'type' => 'MP',
                    'distancePoints' => 3,
                    'isAvailable' => 1
                ],
            ],
            'status' => 200
        ];
        $this->assertEquals(json_encode($expectedResponse), $response->toJson());
    }

    public function testEntryPoint3Input()
    {
        $input = [
            'entryPoint' => 3
        ];
        $request = new GetInfoRequest($input);

        $getInfoAction = new GetInfoAction(
            $this->getParkingMapDaoMock(self::DAO_PARKING_MAP_3),
            $this->getParkingSlotsDaoMock(self::DAO_AVAILABLE_SLOTS)
        );

        $response = $getInfoAction->process($request);

        $expectedResponse = [
            'entryOrExitQuantity' => 3,
            'parkingSlots' => [
                [
                    'id' => 2,
                    'type' => 'MP',
                    'distancePoints' => 1,
                    'isAvailable' => 1
                ],
                [
                    'id' => 3,
                    'type' => 'LP',
                    'distancePoints' => 2,
                    'isAvailable' => 1
                ],
                [
                    'id' => 1,
                    'type' => 'SP',
                    'distancePoints' => 3,
                    'isAvailable' => 1
                ],
            ],
            'status' => 200
        ];
        $this->assertEquals(json_encode($expectedResponse), $response->toJson());
    }
}