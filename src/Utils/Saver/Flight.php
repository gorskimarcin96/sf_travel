<?php

namespace App\Utils\Saver;

use App\Entity\Flight as Entity;
use App\Entity\Search;
use App\Utils\Crawler\Flight\FlightInterface;
use App\Utils\Crawler\Model\URLTrait;
use Doctrine\ORM\EntityManagerInterface;
use Facebook\WebDriver\Exception\Internal\DriverServerDiedException;
use Psr\Log\LoggerInterface;

final readonly class Flight
{
    use URLTrait;

    public function __construct(
        private LoggerInterface $downloaderLogger,
        private EntityManagerInterface $entityManager,
        private bool $simulatePhpWebDriverException = false,
    ) {
    }

    public function save(
        FlightInterface $flight,
        string $fromAirport,
        string $toAirport,
        \DateTimeImmutable $from,
        \DateTimeImmutable $to,
        int $adults,
        int $children,
        Search $search
    ): Search {
        if ($this->simulatePhpWebDriverException) {
            throw new DriverServerDiedException();
        }

        $models = $flight->getFlights($fromAirport, $toAirport, $from, $to, $adults, $children);

        $this->downloaderLogger->info(sprintf('Get %s flights from "%s".', count($models), $flight->getSource()));

        foreach ($models as $model) {
            $entity = (new Entity())
                ->setFromAirport($model->getFromAirport())
                ->setFromStart($model->getFromStart())
                ->setFromEnd($model->getFromEnd())
                ->setFromStops($model->getFromStops())
                ->setToAirport($model->getToAirport())
                ->setToStart($model->getToStart())
                ->setToEnd($model->getToEnd())
                ->setToStops($model->getToStops())
                ->setPrice($model->getMoney()->getPrice())
                ->setPriceForOnePerson($model->getMoney()->isPriceForOnePerson())
                ->setCurrency($model->getMoney()->getCurrency())
                ->setUrl($model->getUrl())
                ->setSource($flight->getSource());

            $this->entityManager->persist($entity);

            $search->addFlight($entity);
        }

        $this->entityManager->flush();

        return $search;
    }
}
