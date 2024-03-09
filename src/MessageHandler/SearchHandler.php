<?php

namespace App\MessageHandler;

use App\Entity\Search as Entity;
use App\Exception\NullException;
use App\Factory\SearchServices;
use App\Message\Search;
use App\Repository\SearchRepositoryInterface;
use App\Utils\Api\Weather\WeatherInterface;
use App\Utils\Crawler\Flight\FlightInterface;
use App\Utils\Crawler\Hotel\HotelInterface;
use App\Utils\Crawler\OptionalTrip\OptionalTripInterface;
use App\Utils\Crawler\PageAttraction\PageAttractionInterface;
use App\Utils\Crawler\Trip\TripInterface;
use App\Utils\Helper\DateTime;
use App\Utils\Saver\Flight;
use App\Utils\Saver\Hotel;
use App\Utils\Saver\OptionalTrip;
use App\Utils\Saver\PageAttraction;
use App\Utils\Saver\Trip;
use App\Utils\Saver\Weather;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Facebook\WebDriver\Exception\PhpWebDriverExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final readonly class SearchHandler
{
    use DateTime;

    public function __construct(
        private LoggerInterface $logger,
        private LoggerInterface $downloaderLogger,
        private SearchServices $tripServices,
        private SearchRepositoryInterface $searchRepository,
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $messageBus,
        private OptionalTrip $optionalTrip,
        private PageAttraction $pageAttraction,
        private Hotel $hotel,
        private Flight $flight,
        private Weather $weather,
        private Trip $trip,
    ) {
    }

    public function __invoke(Search $search, bool $recursive = true, bool $throwable = false): void
    {
        $entity = $this->searchRepository->find($search->getSearchId()) ?? throw new EntityNotFoundException();
        $searchServiceClass = $entity->getServiceTodo();

        if ($searchServiceClass) {
            $this->downloaderLogger->info(sprintf('Getting from %s for %s.', $searchServiceClass, $entity->getId()));

            try {
                /** @var OptionalTripInterface|PageAttractionInterface|HotelInterface|FlightInterface|WeatherInterface|TripInterface $service */
                $service = $this->tripServices->findByClassName($searchServiceClass);

                if ($service instanceof OptionalTripInterface) {
                    $entity = $this->saveOptionalTrips($entity, $service);
                } elseif ($service instanceof PageAttractionInterface) {
                    $entity = $this->savePageAttractions($entity, $service);
                } elseif ($service instanceof HotelInterface) {
                    $entity = $this->saveHotels($entity, $service);
                } elseif ($service instanceof FlightInterface) {
                    $entity = $this->saveFlights($entity, $service);
                } elseif ($service instanceof WeatherInterface) {
                    $entity = $this->saveWeathers($entity, $service);
                } elseif ($service instanceof TripInterface) {
                    $entity = $this->saveTrips($entity, $service);
                } else {
                    throw new \LogicException(sprintf('Service %s is not implemented.', $service::class));
                }

                $entity->addService($searchServiceClass);
            } catch (\Throwable $exception) {
                $this->downloaderLogger->error($exception::class);
                $this->downloaderLogger->error($exception->getMessage());

                $entity->addError($searchServiceClass, [$exception::class, $exception->getMessage()]);

                if ($throwable) {
                    throw $exception;
                }
            }
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        if ($entity->isFinished()) {
            return;
        }

        if (!$recursive) {
            return;
        }

        $this->messageBus->dispatch(new Search($entity->getId() ?? throw new NullException()));
    }

    private function saveOptionalTrips(Entity $entity, OptionalTripInterface $optionalTrip): Entity
    {
        try {
            return $this->optionalTrip->save(
                $optionalTrip,
                $entity->getPlace(),
                $entity->getNation(),
                $entity
            );
        } catch (PhpWebDriverExceptionInterface $exception) {
            $this->logger->error(sprintf($exception::class, $exception));
            $optionalTrip->restartPantherClient();
            $this->logger->notice('Restarted panther client.');

            return $this->optionalTrip->save(
                $optionalTrip,
                $entity->getPlace(),
                $entity->getNation(),
                $entity
            );
        }
    }

    private function savePageAttractions(Entity $entity, PageAttractionInterface $pageAttraction): Entity
    {
        return $this->pageAttraction->save($pageAttraction, $entity->getPlace(), $entity->getNation(), $entity);
    }

    private function saveHotels(Entity $entity, HotelInterface $hotel): Entity
    {
        return $this->hotel->save(
            $hotel,
            $entity->getPlace(),
            $entity->getFrom(),
            $entity->getTo(),
            $entity->getRangeFrom() ?? 1,
            $entity->getRangeTo() ?? $this->countDaysBetween($entity->getFrom(), $entity->getTo()),
            $entity->getHotelFoods(),
            $entity->getHotelStars(),
            $entity->getHotelRate(),
            $entity->getAdults(),
            $entity->getChildren(),
            $entity
        );
    }

    private function saveFlights(Entity $entity, FlightInterface $flight): Entity
    {
        if ($entity->getFromAirport() && $entity->getToAirport()) {
            try {
                return $this->flight->save(
                    $flight,
                    $entity->getFromAirport(),
                    $entity->getToAirport(),
                    $entity->getFrom(),
                    $entity->getTo(),
                    $entity->getAdults(),
                    $entity->getChildren(),
                    $entity
                );
            } catch (PhpWebDriverExceptionInterface $exception) {
                $this->logger->error(sprintf($exception::class, $exception));
                $flight->restartPantherClient();
                $this->logger->notice('Restarted panther client.');

                return $this->flight->save(
                    $flight,
                    $entity->getFromAirport(),
                    $entity->getToAirport(),
                    $entity->getFrom(),
                    $entity->getTo(),
                    $entity->getAdults(),
                    $entity->getChildren(),
                    $entity
                );
            }
        }

        $this->logger->warning('Airports it\'s not defines.');

        return $entity;
    }

    private function saveWeathers(Entity $entity, WeatherInterface $weather): Entity
    {
        return $this->weather->save($weather, $entity);
    }

    private function saveTrips(Entity $entity, TripInterface $trip): Entity
    {
        return $this->trip->save(
            $trip,
            $entity->getPlace(),
            $entity->getFrom(),
            $entity->getTo(),
            $entity->getRangeFrom() ?? 1,
            $entity->getRangeTo() ?? $this->countDaysBetween($entity->getFrom(), $entity->getTo()),
            $entity->getHotelFoods(),
            $entity->getHotelStars(),
            $entity->getHotelRate(),
            $entity->getAdults(),
            $entity->getChildren(),
            $entity,
        );
    }
}
