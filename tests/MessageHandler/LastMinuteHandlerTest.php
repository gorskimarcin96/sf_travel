<?php

namespace App\Tests\MessageHandler;

use App\Entity\LastMinute as EntityLastMinute;
use App\Message\LastMinute;
use App\Tests\ContainerKernelTestCase;
use App\Tests\Mocks\Trip;
use Doctrine\ORM\EntityNotFoundException;

class LastMinuteHandlerTest extends ContainerKernelTestCase
{
    /**
     * @return array<int, array<int, class-string|bool>>
     */
    public function getData(): array
    {
        return [
            [Trip::class, false],
        ];
    }

    /**
     * @dataProvider getData
     *
     * @param class-string $class
     */
    public function testInvokeWithSaveService(string $class, bool $simulatePhpWebDriverException): void
    {
        $this->getEntityManager(true, [$this->getDefaultEntityLastMinute()->setTodo([$class])]);

        $this->getLastMinuteHandler($simulatePhpWebDriverException)(new LastMinute(1));

        $this->assertTrue(true, 'Test completed successfully.');
    }

    public function testInvokeWithSaveServiceRecursive(): void
    {
        $services = [Trip::class, Trip::class];
        $this->getEntityManager(true, [$this->getDefaultEntityLastMinute()->setTodo($services)]);

        $this->getLastMinuteHandler()(new LastMinute(1), true, true);

        $this->assertTrue(true, 'Test completed successfully.');
    }

    public function testInvokeWithSaveServiceNotRecursive(): void
    {
        $services = [Trip::class, Trip::class];
        $this->getEntityManager(true, [$this->getDefaultEntityLastMinute()->setTodo($services)]);

        $this->getLastMinuteHandler()(new LastMinute(1), false, true);

        $this->assertTrue(true, 'Test completed successfully.');
    }

    public function testInvokeWhenServiceIsNotImplemented(): void
    {
        $this->getEntityManager(true, [$this->getDefaultEntityLastMinute()->setTodo([\stdClass::class])]);

        $this->expectException(\LogicException::class);

        $this->getLastMinuteHandler()(new LastMinute(1), true, true);
    }

    public function testInvokeWhenEntityIsNotExists(): void
    {
        $this->expectException(EntityNotFoundException::class);

        $this->getLastMinuteHandler()(new LastMinute(1));
    }

    private function getDefaultEntityLastMinute(): EntityLastMinute
    {
        return (new EntityLastMinute())
            ->setId(1)
            ->setAdults(2)
            ->setChildren(0)
            ->setFromAirport('WAW');
    }
}
