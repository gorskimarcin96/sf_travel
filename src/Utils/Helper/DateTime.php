<?php

namespace App\Utils\Helper;

trait DateTime
{
    public function countDaysBetween(\DateTimeInterface $from, \DateTimeInterface $to): int
    {
        return iterator_count(new \DatePeriod($from, \DateInterval::createFromDateString('1 day'), $to));
    }
}
