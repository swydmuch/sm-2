<?php

declare(strict_types=1);

namespace SWydmuch\SM2;

use SWydmuch\SM2\Response\Quality;

class Repetition
{
    private int $interval = 1;
    private int $count = 0;
    private float $easinessFactor = 2.5;
    private \DateTimeImmutable $date;

    private function __construct(readonly private Clock $clock)
    {
    }

    public static function createFromFirstResponse(Quality $quality, Clock $clock): self
    {
        $repetition = new self($clock);
        $repetition->handleResponse($quality);
        return $repetition;
    }

    public function interval(): int
    {
        return $this->interval;
    }

    public function date(): \DateTimeImmutable
    {
        return $this->date;
    }
    public function handleResponse(Quality $quality): void
    {
        if ($this->isAcceptable($quality)) {
            $this->handleAcceptableResponse($quality);
        } else {
            $this->startFromBeginning();
        }
    }

    private function isAcceptable(Quality $quality): bool
    {
        return $quality === Quality::CORRECT_WITH_DIFFICULTY
            || $quality === Quality::CORRECT_AFTER_HESITATION
            || $quality === Quality::PERFECT;
    }

    private function handleAcceptableResponse(Quality $quality): void
    {
        $this->count++;
        $this->easinessFactor = $this->calculateNewEasinessFactor($quality);
        $this->modifyInterval();
    }

    private function startFromBeginning(): void
    {
        $this->count = 1;
        $this->modifyInterval();
    }

    private function calculateNewEasinessFactor(Quality $quality): float
    {
        return max(
            1.3,
            $this->easinessFactor + (0.1 - (5 - $quality->value) * (0.08 + (5 - $quality->value) * 0.02))
        );
    }
        private function modifyInterval(): void
    {
        $this->interval = $this->calculateInterval();
        $this->date = $this->clock->now()->add(\DateInterval::createFromDateString($this->interval . ' day'));
    }

    private function calculateInterval(): int
    {
        if ($this->count === 1) {
            return 1;
        } elseif ($this->count === 2) {
            return 6;
        } else {
            return (int) round($this->interval * $this->easinessFactor);
        }
    }
}
