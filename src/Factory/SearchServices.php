<?php

namespace App\Factory;

use App\Utils\Api\Weather\WeatherInterface;
use App\Utils\Crawler\Flight\FlightInterface;
use App\Utils\Crawler\Hotel\HotelInterface;
use App\Utils\Crawler\OptionalTrip\OptionalTripInterface;
use App\Utils\Crawler\PageAttraction\PageAttractionInterface;

final readonly class SearchServices
{
    public function __construct(
        private \IteratorAggregate $weathers,
        private \IteratorAggregate $optionalTrips,
        private \IteratorAggregate $flights,
        private \IteratorAggregate $pageAttractions,
        private \IteratorAggregate $hotels,
    ) {
    }

    /**
     * @return OptionalTripInterface[]|PageAttractionInterface[]|HotelInterface[]|FlightInterface[]|WeatherInterface[]
     */
    public function create(): array
    {
        /** @var OptionalTripInterface[]|PageAttractionInterface[]|HotelInterface[]|FlightInterface[]|WeatherInterface[] $services */
        $services = array_merge(
            iterator_to_array($this->weathers->getIterator()),
            iterator_to_array($this->optionalTrips->getIterator()),
            iterator_to_array($this->flights->getIterator()),
            iterator_to_array($this->pageAttractions->getIterator()),
            iterator_to_array($this->hotels->getIterator()),
        );

        return $services;
    }

    public function findByClassName(string $className): OptionalTripInterface|PageAttractionInterface|HotelInterface|FlightInterface|WeatherInterface
    {
        foreach ($this->create() as $object) {
            if ($object::class === $className) {
                return $object;
            }
        }

        throw new \LogicException(sprintf('Service %s is not found.', $className));
    }
}
