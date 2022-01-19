<?php

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CheckCreditTest extends TestCase
{

    public function dataProviderForCreditAmount() : array
    {
        return [
            [true, 10, 4, true],
            [true, 6, 4, false],
            [false, 10, 4, true],
            [false, 6, 4, false]
        ];
    }

    /**
     * function has to start with Test
     * @dataProvider dataProviderForCreditAmount
     */
    public function testCreditAmount(bool $userVar, int $credit, int $hoursBooked, bool $expectedOutput): void
    {
        $user = new User($userVar);
        $user->setCredit($credit);

        $this->assertEquals($expectedOutput, $user->canPay($hoursBooked));
    }
}