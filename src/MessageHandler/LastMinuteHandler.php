<?php

namespace App\MessageHandler;

use App\Entity\LastMinute as Entity;
use App\Exception\NullException;
use App\Factory\SearchServices;
use App\Message\LastMinute;
use App\Repository\LastMinuteRepositoryInterface;
use App\Utils\Api\Weather\WeatherInterface;
use App\Utils\Crawler\Flight\FlightInterface;
use App\Utils\Crawler\Hotel\HotelInterface;
use App\Utils\Crawler\OptionalTrip\OptionalTripInterface;
use App\Utils\Crawler\PageAttraction\PageAttractionInterface;
use App\Utils\Crawler\Trip\TripInterface;
use App\Utils\Helper\DateTime;
use App\Utils\Saver\Trip;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final readonly class LastMinuteHandler
{
    use DateTime;

    public function __construct(
        private LoggerInterface $downloaderLogger,
        private SearchServices $tripServices,
        private LastMinuteRepositoryInterface $lastMinuteRepository,
        private MessageBusInterface $messageBus,
        private Trip $trip,
    ) {
    }

    public function __invoke(LastMinute $lastMinute, bool $recursive = true, bool $throwable = false): void
    {
        $entity = $this->lastMinuteRepository->find($lastMinute->getLastMinuteId()) ?? throw new EntityNotFoundException();
        $searchServiceClass = $entity->getServiceTodo();

        if ($searchServiceClass) {
            try {
                /** @var OptionalTripInterface|PageAttractionInterface|HotelInterface|FlightInterface|WeatherInterface|TripInterface $service */
                $service = $this->tripServices->findByClassName($searchServiceClass);

                $service instanceof TripInterface ?
                    $entity = $this->saveTrips($entity, $service) :
                    throw new \LogicException(sprintf('Service %s is not implemented.', $service::class));

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

        $entity = $this->lastMinuteRepository->save($entity, true);

        if ($entity->isFinished()) {
            return;
        }

        if (!$recursive) {
            return;
        }

        $this->messageBus->dispatch(new LastMinute($entity->getId() ?? throw new NullException()));
    }

    private function saveTrips(Entity $entity, TripInterface $trip): Entity
    {
        return $this->trip->saveByLastMinute(
            $trip,
            $entity->getFrom(),
            $entity->getTo(),
            $entity->getRangeFrom(),
            $entity->getRangeTo(),
            $entity->getHotelFoods(),
            $entity->getHotelStars(),
            $entity->getHotelRate(),
            $entity->getAdults(),
            $entity->getChildren(),
            $entity,
        );
    }
}
