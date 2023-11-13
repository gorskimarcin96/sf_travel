<?php

namespace App\Tests\Behat\Traits;

use App\Entity\OptionalTrip;
use App\Entity\Search;
use App\Entity\TripArticle;
use App\Entity\TripPage;
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

    /** @Given there are searches */
    public function thereAreSearcher(TableNode $tableNode): void
    {
        array_map(function (array $row): void {
            $search = (new Search())
                ->setId($row['id'])
                ->setNation($row['nation'])
                ->setPlace($row['place'])
                ->setFrom(new \DateTimeImmutable($row['from']))
                ->setTo(new \DateTimeImmutable($row['to']))
                ->setAdults($row['adults'] ?? 2)
                ->setChildren($row['children'] ?? 0)
                ->setCreatedAt(new \DateTimeImmutable($row['created_at']));

            $this->entityManager->persist($search);
        }, $tableNode->getHash());

        $this->entityManager->flush();
    }

    /** @Given there are optional trips */
    public function thereAreOptionalTrips(TableNode $tableNode): void
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
        }, $tableNode->getHash());

        $this->entityManager->flush();
    }

    /** @Given there are trip pages */
    public function thereAreTripPages(TableNode $tableNode): void
    {
        array_map(function (array $row): void {
            $tripPage = (new TripPage())
                ->setId($row['id'])
                ->setUrl($row['url'])
                ->setMap($row['map'])
                ->setSource($row['source'])
                ->setSearch($this->entityManager->getRepository(Search::class)->find($row['search_id']));

            $this->entityManager->persist($tripPage);
        }, $tableNode->getHash());

        $this->entityManager->flush();
    }

    /** @Given there are trip page articles */
    public function thereAreTripPageArticles(TableNode $tableNode): void
    {
        array_map(function (array $row): void {
            $pageTrip = $this->entityManager->getRepository(TripPage::class)->find($row['trip_page_id']) ?? throw new EntityNotFoundException();
            $pageTrip->addTripArticle((new TripArticle())
                ->setId($row['id'])
                ->setTitle($row['title'])
                ->setImages(explode(';', (string) $row['images']))
                ->setDescriptions(explode(';', (string) $row['descriptions'])));

            $this->entityManager->persist($pageTrip);
            $this->entityManager->flush();
        }, $tableNode->getHash());
    }
}
