<?php

namespace App\Utils\Saver;

use App\Entity\OptionalTrip as Entity;
use App\Entity\Search;
use App\Utils\Crawler\Model\URLTrait;
use App\Utils\Crawler\OptionalTrip\Model\OptionalTrip as Model;
use App\Utils\Crawler\OptionalTrip\OptionalTripInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final readonly class OptionalTrip
{
    use URLTrait;

    public function __construct(
        private LoggerInterface $downloaderLogger,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function save(OptionalTripInterface $optionalTrip, string $place, string $nation, Search $search): Search
    {
        $models = $optionalTrip->getOptionalTrips($place, $nation);
        $this->downloaderLogger->info(sprintf('Get %s trips from "%s".', count($models), $optionalTrip->getSource()));

        /** @var Model[] $models */
        $models = $this->uniqueByUrl($models);
        $this->downloaderLogger->info(sprintf('Unique models %s.', count($models)));

        foreach ($models as $model) {
            $entity = (new Entity())
                ->setTitle($model->getTitle())
                ->setDescription($model->getDescription())
                ->setUrl($model->getUrl())
                ->setImage($model->getImage())
                ->setSource($optionalTrip->getSource())
                ->setMoney($model->getMoney());

            $this->entityManager->persist($entity);

            $search->addOptionalTrip($entity);
        }

        return $search;
    }
}
