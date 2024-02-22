<?php

namespace App\Utils\Helper;

use App\Exception\NotMatchedException;

trait DateTime
{
    public function countDaysBetween(\DateTimeInterface $from, \DateTimeInterface $to): int
    {
        return iterator_count(new \DatePeriod($from, \DateInterval::createFromDateString('1 day'), $to));
    }

    public function monthPlToNumber(string $month): int
    {
        return match (true) {
            str_starts_with($month, 'sty') => 1,
            str_starts_with($month, 'lut') => 2,
            str_starts_with($month, 'mar') => 3,
            str_starts_with($month, 'kwi') => 4,
            str_starts_with($month, 'maj') => 5,
            str_starts_with($month, 'cze') => 6,
            str_starts_with($month, 'lip') => 7,
            str_starts_with($month, 'sie') => 8,
            str_starts_with($month, 'wrz') => 9,
            str_starts_with($month, 'paź') => 10,
            str_starts_with($month, 'lis') => 11,
            str_starts_with($month, 'gru') => 12,
            default => throw new NotMatchedException($month)
        };
    }
}
