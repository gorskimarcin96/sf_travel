<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240403100108 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE flight ADD price_for_one_person BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE hotel ADD price_for_one_person BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE optional_trip ADD price_for_one_person BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE trip ADD price_for_one_person BOOLEAN NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE trip DROP price_for_one_person');
        $this->addSql('ALTER TABLE optional_trip DROP price_for_one_person');
        $this->addSql('ALTER TABLE hotel DROP price_for_one_person');
        $this->addSql('ALTER TABLE flight DROP price_for_one_person');
    }
}
