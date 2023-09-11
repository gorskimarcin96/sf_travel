<?php

namespace App\Utils\Crawler\OptionalTrip;

use App\Utils\Crawler\OptionalTrip\Model\OptionalTrip;
use App\Utils\Crawler\SourceInterface;

interface OptionalTripInterface extends SourceInterface
{
    /** @return OptionalTrip[] */
    public function getOptionalTrips(string $place, string $nation = null): array;
}
