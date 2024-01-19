<?php

namespace App\Tests\Behat\Traits;

use App\Entity\Flight;
use App\Entity\Hotel;
use App\Entity\Money;
use App\Entity\OptionalTrip;
use App\Entity\Search;
use App\Entity\Trip;
use App\Entity\TripArticle;
use App\Entity\TripPage;
use App\Entity\Weather;
use App\Utils\Enum\Food;
use Behat\Gherkin\Node\TableNode;
use Doctrine\DBAL\Exception as DoctrineException;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;

trait DataLoaderTrait
{
    /**
     * @Given the database is clean
     *
     * @throws DoctrineException|ToolsException
     */
    public function theDatabaseIsClean(): void
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        if (!empty($metadata)) {
            $schemaTool = new SchemaTool($this->entityManager);

            $connection = $this->entityManager->getConnection();
            $connection->executeQuery('DROP SCHEMA public CASCADE');
            $connection->executeQuery('CREATE SCHEMA public');
            $connection->close();
            $connection->connect();

            $schemaTool->createSchema($metadata);
        }
    }

    /**
     * @Given there are searches
     */
    public function thereAreSearcher(TableNode $table): void
    {
        array_map(function (array $row): void {
            $foods = isset($row['food']) ?
                array_map(fn (string $food): Food => Food::from($food), explode(',', $row['food'])) :
                [];

            $search = (new Search())
                ->setId($row['id'])
                ->setNation($row['nation'])
                ->setPlace($row['place'])
                ->setFrom(new \DateTimeImmutable($row['from']))
                ->setTo(new \DateTimeImmutable($row['to']))
                ->setAdults($row['adults'] ?? 2)
                ->setChildren($row['children'] ?? 0)
                ->setRangeFrom($row['range_from'] ?? null)
                ->setRangeTo($row['range_to'] ?? null)
                ->setHotelFoods($foods)
                ->setCreatedAt(new \DateTimeImmutable($row['created_at']));

            $this->entityManager->persist($search);
        }, $table->getHash());

        $this->entityManager->flush();
    }

    /**
     * @Given there are optional trips
     */
    public function thereAreOptionalTrips(TableNode $table): void
    {
        array_map(function (array $row): void {
            $optionalTrip = (new OptionalTrip())
                ->setId($row['id'])
                ->setTitle($row['title'])
                ->setDescription(explode(';', (string) $row['description']))
                ->setUrl($row['url'])
                ->setImage($row['image'])
                ->setSource($row['source'])
                ->setSearch($this->entityManager->getRepository(Search::class)->find($row['search_id']));

            $this->entityManager->persist($optionalTrip);
        }, $table->getHash());

        $this->entityManager->flush();
    }

    /**
     * @Given there are trip pages
     */
    public function thereAreTripPages(TableNode $table): void
    {
        array_map(function (array $row): void {
            $tripPage = (new TripPage())
                ->setId($row['id'])
                ->setUrl($row['url'])
                ->setMap($row['map'])
                ->setSource($row['source'])
                ->setSearch($this->entityManager->getRepository(Search::class)->find($row['search_id']));

            $this->entityManager->persist($tripPage);
        }, $table->getHash());

        $this->entityManager->flush();
    }

    /**
     * @Given there are trip page articles
     */
    public function thereAreTripPageArticles(TableNode $table): void
    {
        array_map(function (array $row): void {
            $pageTrip = $this->entityManager->getRepository(TripPage::class)->find(
                $row['trip_page_id']
            ) ?? throw new EntityNotFoundException();
            $pageTrip->addTripArticle(
                (new TripArticle())
                    ->setId($row['id'])
                    ->setTitle($row['title'])
                    ->setImages(explode(';', (string) $row['images']))
                    ->setDescriptions(explode(';', (string) $row['descriptions']))
            );

            $this->entityManager->persist($pageTrip);
            $this->entityManager->flush();
        }, $table->getHash());
    }

    /**
     * @Given there are hotels
     */
    public function thereAreHotels(TableNode $table): void
    {
        array_map(function (array $row): void {
            $hotel = (new Hotel())
                ->setId($row['id'])
                ->setTitle($row['title'])
                ->setUrl($row['url'])
                ->setImage($row['image'])
                ->setAddress($row['address'])
                ->setDescriptions(explode(';', $row['description']))
                ->setStars($row['stars'])
                ->setRate($row['rate'])
                ->setFood(Food::from($row['food']))
                ->setMoney((new Money())->setPrice($row['price']))
                ->setSource($row['source'])
                ->setFrom(new \DateTimeImmutable($row['from']))
                ->setTo(new \DateTimeImmutable($row['to']))
                ->setSearch($this->entityManager->getRepository(Search::class)->find($row['search_id']));

            $this->entityManager->persist($hotel);
        }, $table->getHash());

        $this->entityManager->flush();
    }

    /**
     * @Given there are flights
     */
    public function thereAreFlights(TableNode $table): void
    {
        array_map(function (array $row): void {
            $flight = (new Flight())
                ->setId($row['id'])
                ->setFromAirport($row['from_airport'])
                ->setFromStart(new \DateTimeImmutable($row['from_start']))
                ->setFromEnd(new \DateTimeImmutable($row['from_end']))
                ->setFromStops($row['from_stops'])
                ->setToAirport($row['to_airport'])
                ->setToStart(new \DateTimeImmutable($row['to_start']))
                ->setToEnd(new \DateTimeImmutable($row['to_end']))
                ->setToStops($row['to_stops'])
                ->setUrl($row['url'])
                ->setMoney((new Money())->setPrice($row['price']))
                ->setSource($row['source'])
                ->setSearch($this->entityManager->getRepository(Search::class)->find($row['search_id']));

            $this->entityManager->persist($flight);
        }, $table->getHash());

        $this->entityManager->flush();
    }

    /**
     * @Given there are weathers
     */
    public function thereAreWeathers(TableNode $table): void
    {
        array_map(function (array $row): void {
            $weather = (new Weather())
                ->setId($row['id'])
                ->setDate(new \DateTime($row['date']))
                ->setTemperature2mMean($row['temperature_2m_mean'])
                ->setPrecipitationHours($row['precipitation_hours'])
                ->setPrecipitationSum($row['precipitation_sum'])
                ->setSource($row['source'])
                ->setSearch($this->entityManager->getRepository(Search::class)->find($row['search_id']));

            $this->entityManager->persist($weather);
        }, $table->getHash());

        $this->entityManager->flush();
    }

    /**
     * @Given there are trips
     */
    public function thereAreTrips(TableNode $table): void
    {
        array_map(function (array $row): void {
            $trip = (new Trip())
                ->setId($row['id'])
                ->setTitle($row['title'])
                ->setStars($row['stars'])
                ->setRate($row['rate'])
                ->setUrl($row['url'])
                ->setImage($row['image'])
                ->setFood(Food::from($row['food']))
                ->setMoney((new Money())->setPrice($row['price']))
                ->setSource($row['source'])
                ->setFrom(new \DateTimeImmutable($row['from']))
                ->setTo(new \DateTimeImmutable($row['to']))
                ->setSearch($this->entityManager->getRepository(Search::class)->find($row['search_id']));

            $this->entityManager->persist($trip);
        }, $table->getHash());

        $this->entityManager->flush();
    }
}
