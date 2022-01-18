<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Entity\Room;
use App\Entity\User;


//class has to end with Test
class CheckRoomAvailabilityTest extends TestCase
{
    public function dataProviderForPremiumRoom() : array
    {
        return [
            [true, true, true],
            [false, false, true],
            [false, true, true],
            [true, false, false]
        ];
    }

    /**
     * function has to start with Test
     */
    //initial version
//    public function testPremiumRoom(): void
//    {
//        $room = new Room(false);
//        $user = new User(false);
//
//        $this->assertTrue($room->canBook($user));
//
//        $room = new Room(true);//premium room, with no premium user
//        $user = new User(false);
//
//        $this->assertFalse($room->canBook($user));
//    }

    //second version:
    /**
     * function has to start with Test
     * @dataProvider dataProviderForPremiumRoom
     */
    public function testPremiumRoom(bool $roomVar, bool $userVar, bool $expectedOutput): void
    {
        $room = new Room($roomVar);
        $user = new User($userVar);

        $this->assertEquals($expectedOutput, $room->canBook($user));
    }


}