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
            $tool = new SchemaTool($this->entityManager);

            $connection = $this->entityManager->getConnection();
            $connection->executeQuery('DROP SCHEMA public CASCADE');
            $connection->executeQuery('CREATE SCHEMA public');
            $connection->close();
            $connection->connect();

            $tool->createSchema($metadata);
        }
    }

    /** @Given there are searches */
    public function thereAreSearcher(TableNode $table): void
    {
        array_map(function (array $row): void {
            $entity = (new Search())
                ->setId($row['id'])
                ->setNation($row['nation'])
                ->setPlace($row['place'])
                ->setCreatedAt(new \DateTimeImmutable($row['created_at']));

            $this->entityManager->persist($entity);
        }, $table->getHash());

        $this->entityManager->flush();
    }

    /** @Given there are optional trips */
    public function thereAreOptionalTrips(TableNode $table): void
    {
        array_map(function (array $row): void {
            $entity = (new OptionalTrip())
                ->setId($row['id'])
                ->setTitle($row['title'])
                ->setDescription(explode(';', (string) $row['description']))
                ->setUrl($row['url'])
                ->setImg($row['img'])
                ->setSource($row['source'])
                ->setSearch($this->entityManager->getRepository(Search::class)->find($row['search_id']));

            $this->entityManager->persist($entity);
        }, $table->getHash());

        $this->entityManager->flush();
    }

    /** @Given there are trip pages */
    public function thereAreTripPages(TableNode $table): void
    {
        array_map(function (array $row): void {
            $entity = (new TripPage())
                ->setId($row['id'])
                ->setUrl($row['url'])
                ->setMap($row['map'])
                ->setSource($row['source'])
                ->setSearch($this->entityManager->getRepository(Search::class)->find($row['search_id']));

            $this->entityManager->persist($entity);
        }, $table->getHash());

        $this->entityManager->flush();
    }

    /** @Given there are trip page articles */
    public function thereAreTripPageArticles(TableNode $table): void
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
        }, $table->getHash());
    }
}
