<?php

namespace App\Factory;

use App\Utils\Api\Weather\WeatherInterface;
use App\Utils\Crawler\Flight\FlightInterface;
use App\Utils\Crawler\Hotel\HotelInterface;
use App\Utils\Crawler\OptionalTrip\OptionalTripInterface;
use App\Utils\Crawler\PageAttraction\PageAttractionInterface;
use App\Utils\Crawler\Trip\TripInterface;

final readonly class SearchServices
{
    public function __construct(
        private \IteratorAggregate $trips,
        private \IteratorAggregate $hotels,
        private \IteratorAggregate $flights,
        private \IteratorAggregate $weathers,
        private \IteratorAggregate $optionalTrips,
        private \IteratorAggregate $pageAttractions,
    ) {
    }

    /**
     * @return TripInterface[]|HotelInterface[]|FlightInterface[]|WeatherInterface[]|OptionalTripInterface[]|PageAttractionInterface[]
     */
    public function create(): array
    {
        /** @var TripInterface[]|HotelInterface[]|FlightInterface[]|WeatherInterface[]|OptionalTripInterface[]|PageAttractionInterface[] $services */
        $services = array_merge(
            iterator_to_array($this->trips->getIterator()),
            iterator_to_array($this->hotels->getIterator()),
            iterator_to_array($this->flights->getIterator()),
            iterator_to_array($this->weathers->getIterator()),
            iterator_to_array($this->optionalTrips->getIterator()),
            iterator_to_array($this->pageAttractions->getIterator()),
        );

        return $services;
    }

    public function findByClassName(string $className): TripInterface|HotelInterface|FlightInterface|WeatherInterface|OptionalTripInterface|PageAttractionInterface
    {
        foreach ($this->create() as $object) {
            if ($object::class === $className) {
                return $object;
            }
        }

        throw new \LogicException(sprintf('Service %s is not found.', $className));
    }
}
