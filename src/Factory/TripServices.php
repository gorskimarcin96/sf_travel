<?php

namespace App\Factory;

use App\Utils\Crawler\OptionalTrip\OptionalTripInterface;
use App\Utils\Crawler\PageAttraction\PageAttractionInterface;

final readonly class TripServices
{
    public function __construct(
        private \IteratorAggregate $optionalTrips,
        private \IteratorAggregate $pageAttractions
    ) {
    }

    /**
     * @return OptionalTripInterface[]|PageAttractionInterface[]
     */
    public function create(): array
    {
        /** @var OptionalTripInterface[]|PageAttractionInterface[] $services */
        $services = array_merge(
            iterator_to_array($this->optionalTrips->getIterator()),
            iterator_to_array($this->pageAttractions->getIterator())
        );

        return $services;
    }

    public function findByClassName(string $className): OptionalTripInterface|PageAttractionInterface
    {
        foreach ($this->create() as $object) {
            if ($object::class === $className) {
                return $object;
            }
        }

        throw new \LogicException(sprintf('Service %s is not found.', $className));
    }
}
