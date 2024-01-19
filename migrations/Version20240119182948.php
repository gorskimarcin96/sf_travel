<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240119182948 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE trip_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE trip (id INT NOT NULL, money_id INT DEFAULT NULL, search_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(1000) NOT NULL, stars INT NOT NULL, rate DOUBLE PRECISION NOT NULL, food VARCHAR(255) NOT NULL, from_at DATE NOT NULL, to_at DATE NOT NULL, image TEXT NOT NULL, source VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7656F53BBF29332C ON trip (money_id)');
        $this->addSql('CREATE INDEX IDX_7656F53B650760A9 ON trip (search_id)');
        $this->addSql('COMMENT ON COLUMN trip.from_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN trip.to_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53BBF29332C FOREIGN KEY (money_id) REFERENCES money (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B650760A9 FOREIGN KEY (search_id) REFERENCES search (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hotel ADD stars INT NOT NULL');
        $this->addSql('ALTER TABLE hotel ADD food VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE hotel ADD from_at DATE NOT NULL');
        $this->addSql('ALTER TABLE hotel ADD to_at DATE NOT NULL');
        $this->addSql('ALTER TABLE hotel ALTER rate SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN hotel.from_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN hotel.to_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE search ADD range_from INT DEFAULT NULL');
        $this->addSql('ALTER TABLE search ADD range_to INT DEFAULT NULL');
        $this->addSql('ALTER TABLE search ADD hotel_foods JSONB NOT NULL');
        $this->addSql('ALTER TABLE search ADD hotel_stars INT DEFAULT NULL');
        $this->addSql('ALTER TABLE search ADD hotel_rate DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE trip_id_seq CASCADE');
        $this->addSql('ALTER TABLE trip DROP CONSTRAINT FK_7656F53BBF29332C');
        $this->addSql('ALTER TABLE trip DROP CONSTRAINT FK_7656F53B650760A9');
        $this->addSql('DROP TABLE trip');
        $this->addSql('ALTER TABLE search DROP range_from');
        $this->addSql('ALTER TABLE search DROP range_to');
        $this->addSql('ALTER TABLE search DROP hotel_foods');
        $this->addSql('ALTER TABLE search DROP hotel_stars');
        $this->addSql('ALTER TABLE search DROP hotel_rate');
        $this->addSql('ALTER TABLE hotel DROP stars');
        $this->addSql('ALTER TABLE hotel DROP food');
        $this->addSql('ALTER TABLE hotel DROP from_at');
        $this->addSql('ALTER TABLE hotel DROP to_at');
        $this->addSql('ALTER TABLE hotel ALTER rate DROP NOT NULL');
    }
}
