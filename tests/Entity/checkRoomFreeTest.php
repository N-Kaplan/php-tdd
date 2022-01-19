<?php

use PHPUnit\Framework\TestCase;
use App\Entity\Room;
use App\Entity\Bookings;

class checkRoomFreeTest extends TestCase
{
    public function dataProviderForTestIsFree(): array
    {
        return [
            [new DateTime("2022-01-17 08:00:00"), new DateTime("2022-01-17 09:00:00"),
            [
            ['startTime' => new DateTime("2022-01-17 10:00:00"), 'endTime' => new DateTime("2022-01-17 16:00:00")],
            ['startTime' => new DateTime("2022-01-17 17:00:00"), 'endTime' => new DateTime("2022-01-17 20:00:00")],
                ],
            true, true],
            [new DateTime("2022-01-17 08:00:00"), new DateTime("2022-01-17 09:00:00"),
                [
                    ['startTime' => new DateTime("2022-01-17 10:00:00"), 'endTime' => new DateTime("2022-01-17 16:00:00")],
                    ['startTime' => new DateTime("2022-01-17 17:00:00"), 'endTime' => new DateTime("2022-01-17 20:00:00")],
                ],
                false, true],
            [new DateTime("2022-01-17 08:00:00"), new DateTime("2022-01-17 12:00:00"),
                [
                    ['startTime' => new DateTime("2022-01-17 10:00:00"), 'endTime' => new DateTime("2022-01-17 16:00:00")],
                    ['startTime' => new DateTime("2022-01-17 17:00:00"), 'endTime' => new DateTime("2022-01-17 20:00:00")],
                ],
                true, false],
            [new DateTime("2022-01-17 10:00:00"), new DateTime("2022-01-17 12:00:00"),
                [
                    ['startTime' => new DateTime("2022-01-17 10:00:00"), 'endTime' => new DateTime("2022-01-17 16:00:00")],
                    ['startTime' => new DateTime("2022-01-17 17:00:00"), 'endTime' => new DateTime("2022-01-17 20:00:00")],
                ],
                true, false],
            [new DateTime("2022-01-17 10:00:00"), new DateTime("2022-01-17 12:00:00"),
                [
                    ['startTime' =>new DateTime("2022-01-17 11:00:00"), 'endTime' => new DateTime("2022-01-17 12:00:00")],
                    ['startTime' =>new DateTime("2022-01-17 17:00:00"), 'endTime' => new DateTime("2022-01-17 20:00:00")],
                ],
                false, false],
            [new DateTime("2022-01-17 10:00:00"), new DateTime("2022-01-17 13:00:00"),
                [
                    ['startTime' => new DateTime("2022-01-17 11:00:00"), 'endTime' => new DateTime("2022-01-17 12:00:00")],
                    ['startTime' => new DateTime("2022-01-17 17:00:00"), 'endTime' => new DateTime("2022-01-17 20:00:00")],
                ],
                true, false],
            [new DateTime("2022-01-17 16:00:00"), new DateTime("2022-01-17 20:00:00"),
                [
                    ['startTime' =>new DateTime("2022-01-17 11:00:00"), 'endTime' => new DateTime("2022-01-17 12:00:00")],
                    ['startTime' =>new DateTime("2022-01-17 17:00:00"), 'endTime' => new DateTime("2022-01-17 20:00:00")],
                ],
                true, false],
            [new DateTime("2022-01-17 16:00:00"), new DateTime("2022-01-17 20:00:00"),
                [
                    ['startTime' =>new DateTime("2022-01-17 11:00:00"), 'endTime' => new DateTime("2022-01-17 12:00:00")],
                    ['startTime' =>new DateTime("2022-01-17 17:00:00"), 'endTime' => new DateTime("2022-01-17 19:00:00")],
                ],
                true, false],
            [new DateTime("2022-01-17 12:00:00"), new DateTime("2022-01-17 13:00:00"),
                [
                    ['startTime' =>new DateTime("2022-01-17 11:00:00"), 'endTime' => new DateTime("2022-01-17 17:00:00")],
                    ['startTime' =>new DateTime("2022-01-17 17:00:00"), 'endTime' => new DateTime("2022-01-17 19:00:00")],
                ],
                true, false],
        ];
    }

    /**
     * function has to start with Test
     * @dataProvider dataProviderForTestIsFree
     */
    public function testIsFree(DateTime $start, DateTime $end, array $reservations, bool $roomVar, bool $expectedOutput): void
    {
        $room = new Room($roomVar);

        $this->assertEquals($expectedOutput, $room->isFree($start, $end, $reservations));
    }
}