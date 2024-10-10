<?php

declare(strict_types=1);


namespace SWydmuch\SM2;

class SystemClock implements Clock
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}