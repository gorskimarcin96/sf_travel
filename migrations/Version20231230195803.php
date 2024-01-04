<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231230195803 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE city_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE weather_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE city (id INT NOT NULL, name_pl VARCHAR(255) NOT NULL, name_en VARCHAR(255) NOT NULL, country_code VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE weather (id INT NOT NULL, city_id INT DEFAULT NULL, search_id INT DEFAULT NULL, date DATE NOT NULL, temperature2m_mean DOUBLE PRECISION NOT NULL, precipitation_hours DOUBLE PRECISION NOT NULL, precipitation_sum DOUBLE PRECISION NOT NULL, source VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4CD0D36E8BAC62AF ON weather (city_id)');
        $this->addSql('CREATE INDEX IDX_4CD0D36E650760A9 ON weather (search_id)');
        $this->addSql('ALTER TABLE weather ADD CONSTRAINT FK_4CD0D36E8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE weather ADD CONSTRAINT FK_4CD0D36E650760A9 FOREIGN KEY (search_id) REFERENCES search (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE city_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE weather_id_seq CASCADE');
        $this->addSql('ALTER TABLE weather DROP CONSTRAINT FK_4CD0D36E8BAC62AF');
        $this->addSql('ALTER TABLE weather DROP CONSTRAINT FK_4CD0D36E650760A9');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE weather');
    }
}
