<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231114171729 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE flight_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE flight (id INT NOT NULL, money_id INT DEFAULT NULL, search_id INT DEFAULT NULL, from_airport VARCHAR(255) NOT NULL, from_start TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, from_end TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, from_stops INT NOT NULL, to_airport VARCHAR(255) NOT NULL, to_start TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, to_end TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, to_stops INT NOT NULL, url VARCHAR(1000) NOT NULL, source VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C257E60EBF29332C ON flight (money_id)');
        $this->addSql('CREATE INDEX IDX_C257E60E650760A9 ON flight (search_id)');
        $this->addSql('COMMENT ON COLUMN flight.from_start IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN flight.from_end IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN flight.to_start IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN flight.to_end IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60EBF29332C FOREIGN KEY (money_id) REFERENCES money (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT FK_C257E60E650760A9 FOREIGN KEY (search_id) REFERENCES search (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE search ADD from_airport VARCHAR(3) NULL');
        $this->addSql('ALTER TABLE search ADD to_airport VARCHAR(3) NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE flight_id_seq CASCADE');
        $this->addSql('ALTER TABLE flight DROP CONSTRAINT FK_C257E60EBF29332C');
        $this->addSql('ALTER TABLE flight DROP CONSTRAINT FK_C257E60E650760A9');
        $this->addSql('DROP TABLE flight');
        $this->addSql('ALTER TABLE search DROP from_airport');
        $this->addSql('ALTER TABLE search DROP to_airport');
    }
}
