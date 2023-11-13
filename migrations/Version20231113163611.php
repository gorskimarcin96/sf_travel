<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231113163611 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE optional_trip RENAME COLUMN img TO image');
        $this->addSql('ALTER TABLE trip_article DROP CONSTRAINT fk_34d64bcbc4663e4');
        $this->addSql('DROP INDEX idx_34d64bcbc4663e4');
        $this->addSql('ALTER TABLE trip_article RENAME COLUMN page_id TO trip_page_id');
        $this->addSql('ALTER TABLE trip_article ADD CONSTRAINT FK_34D64BCB3AAA2649 FOREIGN KEY (trip_page_id) REFERENCES trip_page (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_34D64BCB3AAA2649 ON trip_article (trip_page_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE trip_article DROP CONSTRAINT FK_34D64BCB3AAA2649');
        $this->addSql('DROP INDEX IDX_34D64BCB3AAA2649');
        $this->addSql('ALTER TABLE trip_article RENAME COLUMN trip_page_id TO page_id');
        $this->addSql('ALTER TABLE trip_article ADD CONSTRAINT fk_34d64bcbc4663e4 FOREIGN KEY (page_id) REFERENCES trip_page (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_34d64bcbc4663e4 ON trip_article (page_id)');
        $this->addSql('ALTER TABLE optional_trip RENAME COLUMN image TO img');
    }
}
