<?php

namespace App\Factory;

use App\Utils\Crawler\Hotel\HotelInterface;
use App\Utils\Crawler\OptionalTrip\OptionalTripInterface;
use App\Utils\Crawler\PageAttraction\PageAttractionInterface;

final readonly class TripServices
{
    public function __construct(
        private \IteratorAggregate $optionalTrips,
        private \IteratorAggregate $pageAttractions,
        private \IteratorAggregate $hotels,
    ) {
    }

    /**
     * @return OptionalTripInterface[]|PageAttractionInterface[]|HotelInterface[]
     */
    public function create(): array
    {
        /** @var OptionalTripInterface[]|PageAttractionInterface[]|HotelInterface[] $services */
        $services = array_merge(
            iterator_to_array($this->optionalTrips->getIterator()),
            iterator_to_array($this->pageAttractions->getIterator()),
            iterator_to_array($this->hotels->getIterator()),
        );

        return $services;
    }

    public function findByClassName(string $className): OptionalTripInterface|PageAttractionInterface|HotelInterface
    {
        foreach ($this->create() as $object) {
            if ($object::class === $className) {
                return $object;
            }
        }

        throw new \LogicException(sprintf('Service %s is not found.', $className));
    }
}
