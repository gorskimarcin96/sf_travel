<?php

namespace App\Tests\Mocks;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessageBus implements MessageBusInterface
{
    /**
     * @var object[]
     */
    private array $dispatches = [];

    #[\Override]
    public function dispatch(object $message, array $stamps = []): Envelope
    {
        $this->dispatches[] = $message;

        return new Envelope(new \stdClass());
    }

    public function countDispatchObjects(): int
    {
        return count($this->dispatches);
    }

    public function reset(): void
    {
        $this->dispatches = [];
    }
}
