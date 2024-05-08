<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240508140005 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE last_minute_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE last_minute (id INT NOT NULL, from_at DATE DEFAULT NULL, to_at DATE DEFAULT NULL, adults INT NOT NULL, children INT NOT NULL, from_airport VARCHAR(3) DEFAULT NULL, range_from INT DEFAULT NULL, range_to INT DEFAULT NULL, hotel_foods JSONB NOT NULL, hotel_stars INT DEFAULT NULL, hotel_rate DOUBLE PRECISION DEFAULT NULL, services JSON NOT NULL, todo JSON NOT NULL, errors JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN last_minute.from_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN last_minute.to_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN last_minute.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN last_minute.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE trip ADD last_minute_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B8A82E01D FOREIGN KEY (last_minute_id) REFERENCES last_minute (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7656F53B8A82E01D ON trip (last_minute_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE trip DROP CONSTRAINT FK_7656F53B8A82E01D');
        $this->addSql('DROP SEQUENCE last_minute_id_seq CASCADE');
        $this->addSql('DROP TABLE last_minute');
        $this->addSql('DROP INDEX IDX_7656F53B8A82E01D');
        $this->addSql('ALTER TABLE trip DROP last_minute_id');
    }
}
