<?php

namespace App\Tests\Behat;

use App\Tests\Behat\Traits\DataLoaderTrait;
use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Webmozart\Assert\Assert;

final readonly class FeatureContext implements Context
{
    use DataLoaderTrait;

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /** @Given Messenger bus is empty */
    public function messengerBusIsEmpty(): void
    {
        $this->messengerBusHasNRecords(0);
    }

    /** @Given Messenger bus has :number records */
    public function messengerBusHasNRecords(int $number): void
    {
        Assert::same($this->entityManager->getConnection()->executeQuery('SELECT * FROM messenger_messages')->rowCount(), $number);
    }
}
