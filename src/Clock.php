<?php

declare(strict_types=1);


namespace SWydmuch\SM2;

interface Clock
{
    public function now(): \DateTimeImmutable;
}
