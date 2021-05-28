<?php

use Concrete\Package\ParkingApi\Src\Domain\ParkingMap\ParkingMapService;
use PHPUnit\Framework\TestCase;

class ParkingMapServiceTest extends TestCase
{
    private $parkingMapService;

    public function setUp(): void
    {
        $parkingMapDaoMock = $this->getMockBuilder('Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingMap')
            ->setMethods(['update', 'add', 'getEntryOrExitQuantity'])
            ->getMock();

        $getEntryOrExitQuantity = [
            'id' => 1,
            'entryOrExitQuantity' => 3,
            'dateAdded' => '2021-05-23 00:00:00'
        ];
        $parkingMapDaoMock->method('getEntryOrExitQuantity')->willReturn($getEntryOrExitQuantity);

        $this->parkingMapService = new ParkingMapService($parkingMapDaoMock);
    }

    public function testGetEntryOrExitQuantity()
    {
        $this->assertEquals(3, $this->parkingMapService->getEntryOrExitQuantity());
    }

    public function testValidEntryPoint()
    {
        $this->assertTrue($this->parkingMapService->isValidEntryPoint(1));
        $this->assertTrue($this->parkingMapService->isValidEntryPoint(2));
        $this->assertTrue($this->parkingMapService->isValidEntryPoint(3));
    }

    public function testInvalidEntryPoint()
    {
        $this->assertFalse($this->parkingMapService->isValidEntryPoint(-1));
        $this->assertFalse($this->parkingMapService->isValidEntryPoint(0));
        $this->assertFalse($this->parkingMapService->isValidEntryPoint(4));
    }

    public function testEmptyEntryPoint()
    {
        $this->assertFalse($this->parkingMapService->isValidEntryPoint(''));
        $this->assertFalse($this->parkingMapService->isValidEntryPoint(null));
        $this->assertFalse($this->parkingMapService->isValidEntryPoint(false));
    }

    public function testValidEntryOrExitInput()
    {
        $this->assertTrue($this->parkingMapService->isValidEntryOrExitQuantityInput(3));
        $this->assertTrue($this->parkingMapService->isValidEntryOrExitQuantityInput(4));
        $this->assertTrue($this->parkingMapService->isValidEntryOrExitQuantityInput(5));
        $this->assertTrue($this->parkingMapService->isValidEntryOrExitQuantityInput(10));
    }

    public function testInvalidEntryOrExitInput()
    {
        $this->assertFalse($this->parkingMapService->isValidEntryOrExitQuantityInput(-1));
        $this->assertFalse($this->parkingMapService->isValidEntryOrExitQuantityInput(0));
        $this->assertFalse($this->parkingMapService->isValidEntryOrExitQuantityInput(1));
        $this->assertFalse($this->parkingMapService->isValidEntryOrExitQuantityInput(2));
        $this->assertFalse($this->parkingMapService->isValidEntryOrExitQuantityInput('3'));
    }

    public function testEmptyEntryOrExitInput()
    {
        $this->assertFalse($this->parkingMapService->isValidEntryOrExitQuantityInput(''));
        $this->assertFalse($this->parkingMapService->isValidEntryOrExitQuantityInput(null));
        $this->assertFalse($this->parkingMapService->isValidEntryOrExitQuantityInput(false));
    }
}