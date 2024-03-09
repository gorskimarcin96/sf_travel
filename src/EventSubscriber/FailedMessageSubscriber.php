<?php

namespace App\EventSubscriber;

use App\Message\Search;
use App\Repository\SearchRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;

readonly class FailedMessageSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SearchRepositoryInterface $searchRepository,
        private LoggerInterface $messengerLogger
    ) {
    }

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [WorkerMessageFailedEvent::class => 'onMessageFailed'];
    }

    public function onMessageFailed(WorkerMessageFailedEvent $event): void
    {
        $message = $event->getEnvelope()->getMessage();

        if (Search::class === $message::class) {
            $this->searchRepository->updateFinished($message->getSearchId());

            $this->messengerLogger->error(sprintf('Searcher %s is failed and finished.', $message->getSearchId()));
            $this->messengerLogger->error('Searcher exception', [
                sprintf('%s: %s', $event->getThrowable()::class, $event->getThrowable()->getMessage()),
                sprintf('%s#%s', $event->getThrowable()->getFile(), $event->getThrowable()->getLine()),
            ]);
        }
    }
}
