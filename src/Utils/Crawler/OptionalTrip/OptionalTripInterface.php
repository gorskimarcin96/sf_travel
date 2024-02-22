<?php

namespace App\Utils\Crawler\OptionalTrip;

use App\Entity\SourceInterface;
use App\Utils\Crawler\OptionalTrip\Model\OptionalTrip;

interface OptionalTripInterface extends SourceInterface
{
    /** @return OptionalTrip[] */
    public function getOptionalTrips(string $place, string $nation): array;

    public function restartPantherClient(): void;
}
