<?php

namespace App\Utils\Saver;

use App\Entity\Search;
use App\Utils\Crawler\OptionalTrip\OptionalTripInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final readonly class OptionalTrip
{
    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function save(OptionalTripInterface $service, string $place, string $nation, Search $search): Search
    {
        $models = $service->getOptionalTrips($place, $nation);

        $this->logger->notice(sprintf('Get %s trips from "%s".', count($models), $service->getSource()));

        foreach ($models as $model) {
            $entity = (new \App\Entity\OptionalTrip())
                ->setTitle($model->getTitle())
                ->setDescription($model->getDescription())
                ->setUrl($model->getUrl())
                ->setImg($model->getImg())
                ->setSource($service->getSource())
                ->setMoney($model->getMoney());

            $this->entityManager->persist($entity);

            $search->addOptionalTrip($entity);
        }

        return $search;
    }
}
