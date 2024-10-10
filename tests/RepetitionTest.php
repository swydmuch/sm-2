<?php

declare(strict_types=1);

namespace SWydmuch\SM2\Tests;

use PHPUnit\Framework\TestCase;
use SWydmuch\SM2\Clock;
use SWydmuch\SM2\Repetition;
use SWydmuch\SM2\Response\Quality;

class RepetitionTest extends TestCase
{
    public function testFirstRepetitionShouldCalculate1DayInterval(): void
    {
        $clock = $this->createMock(Clock::class);
        $now = new \DateTimeImmutable('2023-10-09 15:30:00');
        $tomorrow = new \DateTimeImmutable('2023-10-10 15:30:00');
        $clock->method('now')->willReturn($now);
        $repetition = Repetition::createFromFirstResponse(Quality::PERFECT, $clock);
        $this->assertEquals(1, $repetition->interval());
        $this->assertEquals($tomorrow, $repetition->date());
    }

    public function testSecondRepetitionShouldCalculate6DayInterval(): void
    {
        $clock = $this->createMock(Clock::class);
        $repetition = Repetition::createFromFirstResponse(Quality::PERFECT, $clock);
        $repetition->handleResponse(Quality::PERFECT);
        $this->assertEquals(6, $repetition->interval());
    }

    /**
     * @dataProvider badQualityProvider
     */
    public function testBadQualityShouldResetRepetition($badResponseQuality): void
    {
        $clock = $this->createMock(Clock::class);
        $repetition = Repetition::createFromFirstResponse(Quality::PERFECT, $clock);
        $repetition->handleResponse(Quality::PERFECT);
        $repetition->handleResponse($badResponseQuality);
        $this->assertEquals(1, $repetition->interval());
    }

    public function badQualityProvider(): array
    {
        return array(
            array(Quality::INCORRECT_BUT_EASY_RECALL),
            array(Quality::INCORRECT_BUT_REMEMBERED),
            array(Quality::COMPLETE_BLACKOUT)
        );
    }

    /**
     * @dataProvider thirdRepetitionProvider
     */
    public function testThirdRepetitionCalculateIntervalBasedOnResponseQuality(
        $responseQuality,
        $expectedInterval
    ): void {
        $clock = $this->createMock(Clock::class);
        $repetition = Repetition::createFromFirstResponse($responseQuality, $clock);
        $repetition->handleResponse($responseQuality);
        $repetition->handleResponse($responseQuality);
        $this->assertEquals($expectedInterval, $repetition->interval());
    }

    public function thirdRepetitionProvider(): array
    {
        return array(
            'Perfect responses should increase interval' => array(Quality::PERFECT, 17),
            'Correct with hesitation responses should increase interval' => array(Quality::CORRECT_AFTER_HESITATION, 15),
            'Correct with difficulty responses should increase interval' => array(Quality::CORRECT_WITH_DIFFICULTY, 12),
        );
    }

    public function testNineCorrectWithDifficultyShouldIncreaseIntervalWithoutFactorDecreased(): void
    {
        $clock = $this->createMock(Clock::class);
        $repetition = Repetition::createFromFirstResponse(Quality::CORRECT_WITH_DIFFICULTY, $clock);
        $repetition->handleResponse(Quality::CORRECT_WITH_DIFFICULTY);
        $repetition->handleResponse(Quality::CORRECT_WITH_DIFFICULTY);
        $repetition->handleResponse(Quality::CORRECT_WITH_DIFFICULTY);
        $repetition->handleResponse(Quality::CORRECT_WITH_DIFFICULTY);
        $repetition->handleResponse(Quality::CORRECT_WITH_DIFFICULTY);
        $repetition->handleResponse(Quality::CORRECT_WITH_DIFFICULTY);
        $repetition->handleResponse(Quality::CORRECT_WITH_DIFFICULTY);
        $repetition->handleResponse(Quality::CORRECT_WITH_DIFFICULTY);
        $this->assertEquals(185, $repetition->interval());
    }

    public function testBadResponseShouldNotModifyEasinessFactor(): void
    {
        $clock = $this->createMock(Clock::class);
        $repetition = Repetition::createFromFirstResponse(Quality::PERFECT, $clock);
        $repetition->handleResponse(Quality::PERFECT);
        $repetition->handleResponse(Quality::INCORRECT_BUT_EASY_RECALL);
        $repetition->handleResponse(Quality::CORRECT_AFTER_HESITATION);
        $repetition->handleResponse(Quality::CORRECT_AFTER_HESITATION);
        $this->assertEquals(16, $repetition->interval());
    }
}
