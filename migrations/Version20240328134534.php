<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240328134534 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE flight DROP CONSTRAINT fk_c257e60ebf29332c');
        $this->addSql('ALTER TABLE trip DROP CONSTRAINT fk_7656f53bbf29332c');
        $this->addSql('ALTER TABLE hotel DROP CONSTRAINT fk_3535ed9bf29332c');
        $this->addSql('ALTER TABLE optional_trip DROP CONSTRAINT fk_98a9c067bf29332c');
        $this->addSql('DROP SEQUENCE money_id_seq CASCADE');
        $this->addSql('DROP TABLE money');
        $this->addSql('DROP INDEX uniq_c257e60ebf29332c');
        $this->addSql('ALTER TABLE flight ADD price DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE flight ADD currency VARCHAR(3) NOT NULL');
        $this->addSql('ALTER TABLE flight DROP money_id');
        $this->addSql('DROP INDEX uniq_3535ed9bf29332c');
        $this->addSql('ALTER TABLE hotel ADD price DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE hotel ADD currency VARCHAR(3) NOT NULL');
        $this->addSql('ALTER TABLE hotel DROP money_id');
        $this->addSql('DROP INDEX uniq_98a9c067bf29332c');
        $this->addSql('ALTER TABLE optional_trip ADD price DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE optional_trip ADD currency VARCHAR(3) NOT NULL');
        $this->addSql('ALTER TABLE optional_trip DROP money_id');
        $this->addSql('DROP INDEX uniq_7656f53bbf29332c');
        $this->addSql('ALTER TABLE trip ADD price DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE trip ADD currency VARCHAR(3) NOT NULL');
        $this->addSql('ALTER TABLE trip DROP money_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE money_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE money (id INT NOT NULL, price DOUBLE PRECISION NOT NULL, currency VARCHAR(3) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE flight ADD money_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE flight DROP price');
        $this->addSql('ALTER TABLE flight DROP currency');
        $this->addSql('ALTER TABLE flight ADD CONSTRAINT fk_c257e60ebf29332c FOREIGN KEY (money_id) REFERENCES money (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_c257e60ebf29332c ON flight (money_id)');
        $this->addSql('ALTER TABLE trip ADD money_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trip DROP price');
        $this->addSql('ALTER TABLE trip DROP currency');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT fk_7656f53bbf29332c FOREIGN KEY (money_id) REFERENCES money (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_7656f53bbf29332c ON trip (money_id)');
        $this->addSql('ALTER TABLE hotel ADD money_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE hotel DROP price');
        $this->addSql('ALTER TABLE hotel DROP currency');
        $this->addSql('ALTER TABLE hotel ADD CONSTRAINT fk_3535ed9bf29332c FOREIGN KEY (money_id) REFERENCES money (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_3535ed9bf29332c ON hotel (money_id)');
        $this->addSql('ALTER TABLE optional_trip ADD money_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE optional_trip DROP price');
        $this->addSql('ALTER TABLE optional_trip DROP currency');
        $this->addSql('ALTER TABLE optional_trip ADD CONSTRAINT fk_98a9c067bf29332c FOREIGN KEY (money_id) REFERENCES money (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_98a9c067bf29332c ON optional_trip (money_id)');
    }
}
