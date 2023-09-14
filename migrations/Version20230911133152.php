<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230911133152 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE search_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE search (id INT NOT NULL, nation VARCHAR(255) NOT NULL, place VARCHAR(255) NOT NULL, services JSON NOT NULL, todo JSON NOT NULL, errors JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN search.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN search.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE optional_trip ADD search_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE optional_trip ADD CONSTRAINT FK_98A9C067650760A9 FOREIGN KEY (search_id) REFERENCES search (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_98A9C067650760A9 ON optional_trip (search_id)');
        $this->addSql('ALTER TABLE trip_page ADD search_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trip_page ADD CONSTRAINT FK_3F32254B650760A9 FOREIGN KEY (search_id) REFERENCES search (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3F32254B650760A9 ON trip_page (search_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE optional_trip DROP CONSTRAINT FK_98A9C067650760A9');
        $this->addSql('ALTER TABLE trip_page DROP CONSTRAINT FK_3F32254B650760A9');
        $this->addSql('DROP SEQUENCE search_id_seq CASCADE');
        $this->addSql('DROP TABLE search');
        $this->addSql('DROP INDEX IDX_98A9C067650760A9');
        $this->addSql('ALTER TABLE optional_trip DROP search_id');
        $this->addSql('DROP INDEX IDX_3F32254B650760A9');
        $this->addSql('ALTER TABLE trip_page DROP search_id');
    }
}
