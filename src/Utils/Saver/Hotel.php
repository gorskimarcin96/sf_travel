<?php

namespace App\Utils\Saver;

use App\Entity\Search;
use App\Utils\Crawler\Hotel\HotelInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final readonly class Hotel
{
    public function __construct(
        private LoggerInterface $downloaderLogger,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function save(
        HotelInterface $service,
        string $place,
        \DateTimeImmutable $from,
        \DateTimeImmutable $to,
        Search $search
    ): Search {
        $models = $service->getHotels($place, $from, $to);
        $this->downloaderLogger->info(sprintf('Get %s hotels from "%s".', count($models), $service->getSource()));

        foreach ($models as $model) {
            $hotel = (new \App\Entity\Hotel())
                ->setTitle($model->getTitle())
                ->setUrl($model->getUrl())
                ->setAddress($model->getAddress())
                ->setImage($model->getImage())
                ->setRate($model->getRate())
                ->setMoney($model->getMoney())
                ->setDescriptions($model->getDescriptions())
                ->setSource($service->getSource());

            $this->entityManager->persist($hotel);

            $search->addHotel($hotel);
        }

        $this->entityManager->flush();

        return $search;
    }
}
