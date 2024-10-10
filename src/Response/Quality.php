<?php

declare(strict_types=1);

namespace SWydmuch\SM2\Response;

enum Quality: int
{
    case PERFECT = 5;
    case CORRECT_AFTER_HESITATION = 4;
    case CORRECT_WITH_DIFFICULTY = 3;
    case INCORRECT_BUT_EASY_RECALL = 2;
    case INCORRECT_BUT_REMEMBERED = 1;
    case COMPLETE_BLACKOUT = 0;
}
