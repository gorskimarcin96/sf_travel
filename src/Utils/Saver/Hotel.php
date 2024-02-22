<?php

namespace App\Utils\Saver;

use App\Entity\Hotel as Entity;
use App\Entity\Search;
use App\Utils\Crawler\Hotel\HotelInterface;
use App\Utils\Crawler\Hotel\Model\Hotel as Model;
use App\Utils\Crawler\Model\URLTrait;
use App\Utils\Enum\Food;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final readonly class Hotel
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
    public function save(
        HotelInterface $service,
        string $place,
        \DateTimeInterface $from,
        \DateTimeInterface $to,
        int $rangeFrom,
        int $rangeTo,
        array $foods,
        ?int $stars,
        ?float $rate,
        int $adults,
        int $children,
        Search $search
    ): Search {
        $models = $service->getHotels(
            $place,
            $from,
            $to,
            $rangeFrom,
            $rangeTo,
            $foods,
            $stars,
            $rate,
            $adults,
            $children
        );

        $this->downloaderLogger->info(sprintf('Get %s hotels from "%s".', count($models), $service->getSource()));

        /** @var Model[] $models */
        $models = $this->uniqueByUrl($models);

        $this->downloaderLogger->info(sprintf('Unique models %s.', count($models)));

        foreach ($models as $model) {
            $hotel = (new Entity())
                ->setTitle($model->getTitle())
                ->setUrl($model->getUrl())
                ->setAddress($model->getAddress())
                ->setImage($model->getImage())
                ->setStars($model->getStars())
                ->setFood($model->getFood())
                ->setFrom(\DateTimeImmutable::createFromInterface($model->getFrom()))
                ->setTo(\DateTimeImmutable::createFromInterface($model->getTo()))
                ->setRate($model->getRate() ?? 0)
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
