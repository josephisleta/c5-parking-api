<?php

use Concrete\Package\ParkingApi\Src\Application\Parking\Actions\UnParkAction;
use Concrete\Package\ParkingApi\Src\Application\Parking\Requests\UnParkRequest;
use PHPUnit\Framework\TestCase;

class UnParkTest extends TestCase
{
    private $unParkAction;

    const DAO_VEHICLE = [
        'plateNumber' => 'TEST123',
        'type' => 'M',
        'color' => ''
    ];

    const DAO_PARKING_SLIP_ONGOING_1 = [
        'id' => 1,
        'parkingSlotId' => 100,
        'plateNumber' => 'TEST123',
        'entryTime' => '2021-05-23 00:00:00',
        'exitTime' => null,
        'fee' => null
    ];

    const DAO_PARKING_SLIP_EXITED_1 = [
        'id' => 1,
        'parkingSlotId' => 100,
        'plateNumber' => 'TEST123',
        'entryTime' => '2021-05-23 00:00:00',
        'exitTime' => '2021-05-23 02:00:00',
        'fee' => 40
    ];

    const DAO_PARKING_SLOT_TAKEN_MP_1 = [
        'id' => 1,
        'type' => 'MP',
        'distancePoints' => 'a:3:{i:0;i:3;i:1;i:4;i:2;i:5;}', // 3 4 5
        'isAvailable' => 0,
        'plateNumber' => null,
        'parkingSlipId' => null
    ];

    public function setUp(): void
    {
        $this->unParkAction = new UnParkAction($this->getParkingSlotsDaoMock(), $this->getVehiclesDaoMock(self::DAO_VEHICLE), $this->getParkingSlipsDaoMock(self::DAO_PARKING_SLIP_ONGOING_1));
    }

    /**
     * @param array|null $parkingSlotData
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @throws ReflectionException
     */
    private function getParkingSlotsDaoMock($parkingSlotData = null)
    {
        $parkingSlotsDaoMock = $this->getMockBuilder('Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingSlots')
            ->setMethods(['getById', 'updateAvailability'])
            ->getMock();
        $parkingSlotsDaoMock->method('getById')->willReturn($parkingSlotData);

        return $parkingSlotsDaoMock;
    }

    /**
     * @param array|null $vehicleData
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @throws ReflectionException
     */
    private function getVehiclesDaoMock($vehicleData = null)
    {
        $vehiclesDaoMock = $this->getMockBuilder('Concrete\Package\ParkingApi\Src\Infrastructure\Dao\Vehicles')
            ->setMethods(['get'])
            ->getMock();
        $vehiclesDaoMock->method('get')->willReturn($vehicleData);

        return $vehiclesDaoMock;
    }

    /**
     * @param array|null $parkingSlipsData
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @throws ReflectionException
     */
    private function getParkingSlipsDaoMock($parkingSlipsData = null)
    {
        $parkingSlipsDaoMock = $this->getMockBuilder('Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingSlips')
            ->setMethods(['getLatestByPlateNumber', 'update'])
            ->getMock();
        $parkingSlipsDaoMock->method('getLatestByPlateNumber')->willReturn($parkingSlipsData);

        return $parkingSlipsDaoMock;
    }

    public function testInvalidRequest()
    {
        $invalidRequests = [
            null,
            [
                'plateNumber' => ''
            ]
        ];

        foreach ($invalidRequests as $invalidRequest) {
            $request = new UnParkRequest($invalidRequest);

            $response = $this->unParkAction->process($request);

            $expectedResponse = [
                'errorMessage' => 'Please enter the plate number.',
                'errorCode' => 221
            ];
            $this->assertEquals(json_encode($expectedResponse), $response->toJson());
        }
    }

    public function testInvalidPlateNumber()
    {
        $invalidPlateNumbers = [
            -1, '$#@#$', 'LENGTHIS9', 'ASDG-123'
        ];

        foreach ($invalidPlateNumbers as $invalidPlateNumber) {
            $invalidRequest = [
                'plateNumber' => $invalidPlateNumber
            ];
            $request = new UnParkRequest($invalidRequest);

            $response = $this->unParkAction->process($request);

            $expectedResponse = [
                'errorMessage' => 'Please enter a valid plate number (alphanumeric 1-8 characters)',
                'errorCode' => 222
            ];
            $this->assertEquals(json_encode($expectedResponse), $response->toJson());
        }
    }

    public function testNotExistingPlateNumber()
    {
        $validRequest = [
            'plateNumber' => 'NOTEXIST'
        ];
        $request = new UnParkRequest($validRequest);

        $unParkAction = new UnParkAction(
            $this->getParkingSlotsDaoMock(),
            $this->getVehiclesDaoMock(null),
            $this->getParkingSlipsDaoMock()
        );

        $response = $unParkAction->process($request);

        $expectedResponse = [
            'errorMessage' => 'The plate number does not exist in our system.',
            'errorCode' => 225
        ];
        $this->assertEquals(json_encode($expectedResponse), $response->toJson());
    }

    public function testNotExistingParkingSlip()
    {
        $validRequest = [
            'plateNumber' => 'TEST123'
        ];
        $request = new UnParkRequest($validRequest);

        $unParkAction = new UnParkAction(
            $this->getParkingSlotsDaoMock(),
            $this->getVehiclesDaoMock(self::DAO_VEHICLE),
            $this->getParkingSlipsDaoMock(null)
        );

        $response = $unParkAction->process($request);

        $expectedResponse = [
            'errorMessage' => 'Parking slip for plate number TEST123 does not exist.',
            'errorCode' => 244
        ];
        $this->assertEquals(json_encode($expectedResponse), $response->toJson());
    }

    public function testAlreadyExitedParkingSlip()
    {
        $validRequest = [
            'plateNumber' => 'TEST123'
        ];
        $request = new UnParkRequest($validRequest);

        $unParkAction = new UnParkAction(
            $this->getParkingSlotsDaoMock(),
            $this->getVehiclesDaoMock(self::DAO_VEHICLE),
            $this->getParkingSlipsDaoMock(self::DAO_PARKING_SLIP_EXITED_1)
        );

        $response = $unParkAction->process($request);

        $expectedResponse = [
            'errorMessage' => 'The vehicle has already left the parking lot.',
            'errorCode' => 226
        ];
        $this->assertEquals(json_encode($expectedResponse), $response->toJson());
    }

    public function testSuccessfulUnPark()
    {
        $validRequest = [
            'plateNumber' => 'TEST123'
        ];
        $request = new UnParkRequest($validRequest);

        $unParkAction = new UnParkAction(
            $this->getParkingSlotsDaoMock(self::DAO_PARKING_SLOT_TAKEN_MP_1),
            $this->getVehiclesDaoMock(self::DAO_VEHICLE),
            $this->getParkingSlipsDaoMock(self::DAO_PARKING_SLIP_ONGOING_1)
        );

        $response = $unParkAction->process($request);

        $this->assertEquals(1, $response->getParkingSlip()->getId());
        $this->assertEquals('TEST123', $response->getParkingSlip()->getPlateNumber());
        $this->assertNotEmpty($response->getParkingSlip()->getEntryTime());
        $this->assertNotEmpty($response->getParkingSlip()->getFee());
    }
}