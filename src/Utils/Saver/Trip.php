<?php

namespace App\Utils\Saver;

use App\Entity\LastMinute;
use App\Entity\Search;
use App\Entity\Trip as Entity;
use App\Utils\Crawler\Model\URLTrait;
use App\Utils\Crawler\Trip\Model\Trip as Model;
use App\Utils\Crawler\Trip\TripInterface;
use App\Utils\Enum\Food;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final readonly class Trip
{
    use URLTrait;

    public function __construct(
        private LoggerInterface $downloaderLogger,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @param Food[] $foods
     */
    public function saveBySearch(
        TripInterface $trip,
        string $place,
        \DateTimeImmutable $from,
        \DateTimeImmutable $to,
        int $rangeFrom,
        int $rangeTo,
        array $foods,
        ?int $stars,
        ?float $rate,
        int $adults,
        int $children,
        Search $search
    ): Search {
        $models = $trip->getTrips($place, $from, $to, $rangeFrom, $rangeTo, $foods, $stars, $rate, $adults + $children);

        $this->downloaderLogger->info(sprintf('Get %s trips from "%s".', count($models), $trip->getSource()));

        foreach ($models as $model) {
            $entity = $this->createEntityByModel($model, $trip);

            $this->entityManager->persist($entity);

            $search->addTrip($entity);
        }

        $this->entityManager->flush();

        return $search;
    }

    /**
     * @param Food[] $foods
     */
    public function saveByLastMinute(
        TripInterface $trip,
        ?\DateTimeImmutable $from,
        ?\DateTimeImmutable $to,
        ?int $rangeFrom,
        ?int $rangeTo,
        array $foods,
        ?int $stars,
        ?float $rate,
        int $adults,
        int $children,
        LastMinute $lastMinute
    ): LastMinute {
        $models = $trip->getTrips(null, $from, $to, $rangeFrom, $rangeTo, $foods, $stars, $rate, $adults + $children);

        $this->downloaderLogger->info(sprintf('Get %s trips from "%s".', count($models), $trip->getSource()));

        foreach ($models as $model) {
            $entity = $this->createEntityByModel($model, $trip);

            $this->entityManager->persist($entity);

            $lastMinute->addTrip($entity);
        }

        $this->entityManager->flush();

        return $lastMinute;
    }

    private function createEntityByModel(Model $model, TripInterface $trip): Entity
    {
        return (new Entity())
            ->setTitle($model->getTitle())
            ->setStars($model->getStars())
            ->setRate($model->getRate())
            ->setImage($model->getImage())
            ->setFood($model->getFood())
            ->setPrice($model->getMoney()->getPrice())
            ->setPriceForOnePerson($model->getMoney()->isPriceForOnePerson())
            ->setCurrency($model->getMoney()->getCurrency())
            ->setUrl($model->getUrl())
            ->setFrom(\DateTimeImmutable::createFromInterface($model->getFrom()))
            ->setTo(\DateTimeImmutable::createFromInterface($model->getTo()))
            ->setSource($trip->getSource());
    }
}
