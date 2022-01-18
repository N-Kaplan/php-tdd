<?php

use App\Entity\Bookings;
use PHPUnit\Framework\TestCase;
use App\Entity\Room;

class CheckTimeLimitTest extends TestCase
{
    public function dataProviderForTimeFrame() : array
    {
        return [
            [new DateTime("2022-01-17 16:00:00"), new DateTime("2022-01-17 20:00:00"), true],
            [new DateTime("2022-01-17 16:00:00"), new DateTime("2022-01-17 17:00:00"), true],
            [new DateTime("2022-01-17 22:00:00"), new DateTime("2022-01-18 02:00:00"), true],
            [new DateTime("2022-01-17 02:00:00"), new DateTime("2022-01-17 16:00:00"), false],
            [new DateTime("2022-01-17 17:00:00"), new DateTime("2022-01-17 16:00:00"), false]
        ];
    }

    //assertEquals: https://phpunit.readthedocs.io/en/stable/assertions.html#assertequals
    /**
     * function has to start with Test
     * @dataProvider dataProviderForTimeFrame
     */
    public function testTimeFrame(DateTime $start, DateTime $end, bool $expectedOutput): void
    {
        $booking = new Bookings($start, $end);
        $startTime = $start;
        $endTime = $end;

        $this->assertEquals($expectedOutput, $booking->canBookTime($startTime, $endTime));
    }
}