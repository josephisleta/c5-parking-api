<?php

use Concrete\Package\ParkingApi\Src\Helpers\DatetimeHelper;
use PHPUnit\Framework\TestCase;

class DatetimeHelperTest extends TestCase
{
    private $datetimeHelper;

    public function setUp(): void
    {
        $this->datetimeHelper = new DatetimeHelper();
    }

    public function testEmpty()
    {
        $hrsDiff = $this->datetimeHelper->getHrsDiff('', '');
        $this->assertEquals(0, $hrsDiff);

        $hrsDiff = $this->datetimeHelper->getHrsDiff(null, null);
        $this->assertEquals(0, $hrsDiff);
    }

    public function testHrsDiff()
    {
        $hrsDiff = $this->datetimeHelper->getHrsDiff('2021-05-23 00:00:00', '2021-05-23 00:45:00');
        $this->assertEquals(0.75, $hrsDiff);

        $hrsDiff = $this->datetimeHelper->getHrsDiff('2021-05-23 00:00:00', '2021-05-23 01:00:00');
        $this->assertEquals(1, $hrsDiff);

        $hrsDiff = $this->datetimeHelper->getHrsDiff('2021-05-23 00:00:00', '2021-05-23 01:30:00');
        $this->assertEquals(1.5, $hrsDiff);

        $hrsDiff = $this->datetimeHelper->getHrsDiff('2021-05-23 00:00:00', '2021-05-23 02:00:00');
        $this->assertEquals(2, $hrsDiff);

        $hrsDiff = $this->datetimeHelper->getHrsDiff('2021-05-23 00:00:00', '2021-05-23 02:15:00');
        $this->assertEquals(2.25, $hrsDiff);

        $hrsDiff = $this->datetimeHelper->getHrsDiff('2021-05-23 00:00:00', '2021-05-24 00:00:00');
        $this->assertEquals(24, $hrsDiff);
    }
}