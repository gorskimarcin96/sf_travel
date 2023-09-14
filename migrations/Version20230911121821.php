<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230911121821 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE money_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE optional_trip_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE money (id INT NOT NULL, price NUMERIC(10, 2) NOT NULL, currency VARCHAR(3) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE optional_trip (id INT NOT NULL, money_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description JSON NOT NULL, url VARCHAR(255) NOT NULL, img TEXT NOT NULL, source VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_98A9C067BF29332C ON optional_trip (money_id)');
        $this->addSql('ALTER TABLE optional_trip ADD CONSTRAINT FK_98A9C067BF29332C FOREIGN KEY (money_id) REFERENCES money (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE money_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE optional_trip_id_seq CASCADE');
        $this->addSql('ALTER TABLE optional_trip DROP CONSTRAINT FK_98A9C067BF29332C');
        $this->addSql('DROP TABLE money');
        $this->addSql('DROP TABLE optional_trip');
    }
}
