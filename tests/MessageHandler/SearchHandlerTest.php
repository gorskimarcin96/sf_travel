<?php

namespace App\Tests\MessageHandler;

use App\Entity\Search as EntitySearch;
use App\Message\Search;
use App\Tests\ContainerKernelTestCase;
use App\Tests\Mocks\Flight;
use App\Tests\Mocks\Hotel;
use App\Tests\Mocks\OptionalTrip;
use App\Tests\Mocks\PageAttraction;
use App\Tests\Mocks\Trip;
use App\Tests\Mocks\Weather;
use Doctrine\ORM\EntityNotFoundException;

class SearchHandlerTest extends ContainerKernelTestCase
{
    /**
     * @return array<int, array<int, class-string|bool>>
     */
    public function getData(): array
    {
        return [
            [OptionalTrip::class, false],
            [PageAttraction::class, false],
            [Hotel::class, false],
            [Flight::class, false],
            [Weather::class, false],
            [Trip::class, false],
            [OptionalTrip::class, true],
            [Flight::class, true],
        ];
    }

    /**
     * @dataProvider getData
     *
     * @param class-string $class
     */
    public function testInvokeWithSaveService(string $class, bool $simulatePhpWebDriverException): void
    {
        $this->getEntityManager(true, [$this->getDefaultEntitySearch()->setTodo([$class])]);

        $this->getSearchHandler($simulatePhpWebDriverException)(new Search(1));

        $this->assertTrue(true, 'Test completed successfully.');
    }

    public function testInvokeWithSaveFlightWithoutAirports(): void
    {
        $searchEntity = $this->getDefaultEntitySearch()
            ->setFromAirport(null)
            ->setToAirport(null)
            ->setTodo([Flight::class]);

        $this->getEntityManager(true, [$searchEntity]);

        $this->getSearchHandler()(new Search(1));

        $this->assertTrue(true, 'Test completed successfully.');
    }

    public function testInvokeWithSaveServiceRecursive(): void
    {
        $services = [OptionalTrip::class, PageAttraction::class];
        $this->getEntityManager(true, [$this->getDefaultEntitySearch()->setTodo($services)]);

        $this->getSearchHandler()(new Search(1), true, true);

        $this->assertTrue(true, 'Test completed successfully.');
    }

    public function testInvokeWithSaveServiceNotRecursive(): void
    {
        $services = [OptionalTrip::class, PageAttraction::class];
        $this->getEntityManager(true, [$this->getDefaultEntitySearch()->setTodo($services)]);

        $this->getSearchHandler()(new Search(1), false, true);

        $this->assertTrue(true, 'Test completed successfully.');
    }

    public function testInvokeWhenServiceIsNotImplemented(): void
    {
        $this->getEntityManager(true, [$this->getDefaultEntitySearch()->setTodo([\stdClass::class])]);

        $this->expectException(\LogicException::class);

        $this->getSearchHandler()(new Search(1), true, true);
    }

    public function testInvokeWhenEntityIsNotExists(): void
    {
        $this->expectException(EntityNotFoundException::class);

        $this->getSearchHandler()(new Search(1));
    }

    private function getDefaultEntitySearch(): EntitySearch
    {
        return (new EntitySearch())
            ->setId(1)
            ->setPlace('Zakynthos')
            ->setNation('Grecja')
            ->setFrom(new \DateTimeImmutable('01-01-2020'))
            ->setTo(new \DateTimeImmutable('07-01-2020'))
            ->setAdults(2)
            ->setChildren(0)
            ->setFromAirport('WAW')
            ->setToAirport('ZTH');
    }
}
