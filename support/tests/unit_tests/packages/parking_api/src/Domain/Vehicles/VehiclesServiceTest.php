<?php

use Concrete\Package\ParkingApi\Src\Domain\Vehicles\VehiclesService;
use PHPUnit\Framework\TestCase;

class VehiclesServiceTest extends TestCase
{
    private $vehiclesService;

    public function setUp(): void
    {
        $parkingMapDaoMock = $this->getMockBuilder('Concrete\Package\ParkingApi\Src\Infrastructure\Dao\ParkingMap')->getMock();
        $this->vehiclesService = new VehiclesService($parkingMapDaoMock);
    }

    public function testValidPlateNumber()
    {
        $this->assertTrue($this->vehiclesService->isValidPlateNumber('QWERT123'));
        $this->assertTrue($this->vehiclesService->isValidPlateNumber('123QWERT'));
        $this->assertTrue($this->vehiclesService->isValidPlateNumber('12345'));
        $this->assertTrue($this->vehiclesService->isValidPlateNumber('1'));
        $this->assertTrue($this->vehiclesService->isValidPlateNumber('ABC'));
        $this->assertTrue($this->vehiclesService->isValidPlateNumber('A'));
    }

    public function testInvalidPlateNumber()
    {
        $this->assertFalse($this->vehiclesService->isValidPlateNumber('QWERTY 123'));
        $this->assertFalse($this->vehiclesService->isValidPlateNumber('QWERTY-123'));
        $this->assertFalse($this->vehiclesService->isValidPlateNumber('!@#$%^'));
        $this->assertFalse($this->vehiclesService->isValidPlateNumber('QWERTY!'));
        $this->assertFalse($this->vehiclesService->isValidPlateNumber('@QWERTY'));
        $this->assertFalse($this->vehiclesService->isValidPlateNumber('123@QWERTY'));
    }

    public function testEmptyPlateNumber()
    {
        $this->assertFalse($this->vehiclesService->isValidPlateNumber(''));
        $this->assertFalse($this->vehiclesService->isValidPlateNumber(null));
        $this->assertFalse($this->vehiclesService->isValidPlateNumber(false));
    }

    public function testValidType()
    {
        $this->assertTrue($this->vehiclesService->isValidType('S'));
        $this->assertTrue($this->vehiclesService->isValidType('M'));
        $this->assertTrue($this->vehiclesService->isValidType('L'));
    }

    public function testInvalidType()
    {
        $this->assertFalse($this->vehiclesService->isValidType('A'));
        $this->assertFalse($this->vehiclesService->isValidType('B'));
        $this->assertFalse($this->vehiclesService->isValidType('Z'));
        $this->assertFalse($this->vehiclesService->isValidType(1));
        $this->assertFalse($this->vehiclesService->isValidType('SP'));
    }

    public function testEmptyType()
    {
        $this->assertFalse($this->vehiclesService->isValidType(''));
        $this->assertFalse($this->vehiclesService->isValidType(null));
        $this->assertFalse($this->vehiclesService->isValidType(false));
    }

    public function testValidColor()
    {
        $this->assertTrue($this->vehiclesService->isValidColor(''));
        $this->assertTrue($this->vehiclesService->isValidColor('white'));
        $this->assertTrue($this->vehiclesService->isValidColor('black'));
        $this->assertTrue($this->vehiclesService->isValidColor('yellow green'));
        $this->assertTrue($this->vehiclesService->isValidColor('zxcvb'));
    }

    public function testInvalidColor()
    {
        $this->assertFalse($this->vehiclesService->isValidColor(1));
        $this->assertFalse($this->vehiclesService->isValidColor('yellow123'));
        $this->assertFalse($this->vehiclesService->isValidColor('black!@#'));
        $this->assertFalse($this->vehiclesService->isValidColor('!'));
        $this->assertFalse($this->vehiclesService->isValidColor('123'));
    }

    public function testEmptyColor()
    {
        $this->assertTrue($this->vehiclesService->isValidColor(''));
        $this->assertTrue($this->vehiclesService->isValidColor(null));
        $this->assertTrue($this->vehiclesService->isValidColor(false));
    }
}