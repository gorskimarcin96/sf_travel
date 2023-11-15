<?php

namespace App\MessageHandler;

use App\Exception\NullException;
use App\Factory\TripServices;
use App\Message\Search;
use App\Repository\SearchRepository;
use App\Utils\Crawler\Flight\FlightInterface;
use App\Utils\Crawler\Hotel\HotelInterface;
use App\Utils\Crawler\OptionalTrip\OptionalTripInterface;
use App\Utils\Crawler\PageAttraction\PageAttractionInterface;
use App\Utils\Saver\Flight;
use App\Utils\Saver\Hotel;
use App\Utils\Saver\OptionalTrip;
use App\Utils\Saver\PageAttraction;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Facebook\WebDriver\Exception\PhpWebDriverExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class SearchHandler implements MessageHandlerInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private LoggerInterface $downloaderLogger,
        private TripServices $tripServices,
        private SearchRepository $searchRepository,
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $messageBus,
        private OptionalTrip $optionalTrip,
        private PageAttraction $pageAttraction,
        private Hotel $hotel,
        private Flight $flight,
    ) {
    }

    public function __invoke(Search $search, bool $recursive = true): void
    {
        $entity = $this->searchRepository->find($search->getSearchId()) ?? throw new EntityNotFoundException();
        $searchServiceClass = $entity->getServiceTodo();

        if ($searchServiceClass) {
            try {
                /** @var OptionalTripInterface|PageAttractionInterface|HotelInterface|FlightInterface $service */
                $service = $this->tripServices->findByClassName($searchServiceClass);

                if ($service instanceof OptionalTripInterface) {
                    try {
                        $entity = $this->optionalTrip->save($service, $entity->getPlace(), $entity->getNation(), $entity);
                    } catch (PhpWebDriverExceptionInterface $exception) {
                        $this->logger->error(sprintf($exception::class, $exception));
                        $service->restartPantherClient();

                        $this->logger->notice('Restarted panther client.');
                        $entity = $this->optionalTrip->save($service, $entity->getPlace(), $entity->getNation(), $entity);
                    }
                } elseif ($service instanceof PageAttractionInterface) {
                    $entity = $this->pageAttraction->save($service, $entity->getPlace(), $entity->getNation(), $entity);
                } elseif ($service instanceof HotelInterface) {
                    $entity = $this->hotel->save(
                        $service,
                        $entity->getPlace(),
                        $entity->getFrom(),
                        $entity->getTo(),
                        $entity->getAdults(),
                        $entity->getChildren(),
                        $entity
                    );
                } elseif ($service instanceof FlightInterface) {
                    $entity->getFromAirport() && $entity->getToAirport() ? $entity = $this->flight->save(
                        $service,
                        $entity->getFromAirport(),
                        $entity->getToAirport(),
                        $entity->getFrom(),
                        $entity->getTo(),
                        $entity->getAdults(),
                        $entity->getChildren(),
                        $entity
                    ) : $this->logger->warning('Airports it\'s not defines.');
                } else {
                    throw new \LogicException(sprintf('Service %s is not implemented.', $service::class));
                }

                $entity->addService($searchServiceClass);
            } catch (\Throwable $exception) {
                $this->downloaderLogger->error($exception::class);
                $this->downloaderLogger->error($exception->getMessage());

                $entity->addError($searchServiceClass, [$exception::class, $exception->getMessage()]);
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
}
