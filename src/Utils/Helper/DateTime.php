<?php

namespace App\Utils\Helper;

use App\Exception\NotMatchedException;

trait DateTime
{
    public function countDaysBetween(\DateTimeInterface $from, \DateTimeInterface $to): int
    {
        return iterator_count(new \DatePeriod($from, \DateInterval::createFromDateString('1 day'), $to));
    }

    public function monthToNumber(string $month): int
    {
        $month = strtolower($month);

        return match (true) {
            str_starts_with($month, 'sty'), str_starts_with($month, 'jan') => 1,
            str_starts_with($month, 'lut'), str_starts_with($month, 'feb') => 2,
            str_starts_with($month, 'mar') => 3,
            str_starts_with($month, 'kwi'), str_starts_with($month, 'apr') => 4,
            str_starts_with($month, 'maj'), str_starts_with($month, 'may') => 5,
            str_starts_with($month, 'cze'), str_starts_with($month, 'jun') => 6,
            str_starts_with($month, 'lip'), str_starts_with($month, 'jul') => 7,
            str_starts_with($month, 'sie'), str_starts_with($month, 'aug') => 8,
            str_starts_with($month, 'wrz'), str_starts_with($month, 'sep') => 9,
            str_starts_with($month, 'paź'), str_starts_with($month, 'oct') => 10,
            str_starts_with($month, 'lis'), str_starts_with($month, 'nov') => 11,
            str_starts_with($month, 'gru'), str_starts_with($month, 'dec') => 12,
            default => throw new NotMatchedException($month)
        };
    }
}
