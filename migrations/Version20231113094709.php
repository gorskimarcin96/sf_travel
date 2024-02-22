<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231113094709 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE hotel_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE hotel (id INT NOT NULL, money_id INT DEFAULT NULL, search_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(1000) NOT NULL, image TEXT NOT NULL, address VARCHAR(255) NOT NULL, descriptions JSON NOT NULL, rate DOUBLE PRECISION DEFAULT NULL, source VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3535ED9BF29332C ON hotel (money_id)');
        $this->addSql('CREATE INDEX IDX_3535ED9650760A9 ON hotel (search_id)');
        $this->addSql('ALTER TABLE hotel ADD CONSTRAINT FK_3535ED9BF29332C FOREIGN KEY (money_id) REFERENCES money (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hotel ADD CONSTRAINT FK_3535ED9650760A9 FOREIGN KEY (search_id) REFERENCES search (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE search ADD from_at DATE NOT NULL');
        $this->addSql('ALTER TABLE search ADD to_at DATE NOT NULL');
        $this->addSql('ALTER TABLE search ADD adults INT NOT NULL');
        $this->addSql('ALTER TABLE search ADD children INT NOT NULL');
        $this->addSql('COMMENT ON COLUMN search.from_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN search.to_at IS \'(DC2Type:date_immutable)\'');

        if ('test' !== $_ENV['APP_ENV']) {
            $this->addSql('DROP SEQUENCE messenger_messages_id_seq CASCADE');
            $this->addSql('DROP TABLE messenger_messages');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE hotel_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE messenger_messages_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_75ea56e016ba31db ON messenger_messages (delivered_at)');
        $this->addSql('CREATE INDEX idx_75ea56e0e3bd61ce ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX idx_75ea56e0fb7336f0 ON messenger_messages (queue_name)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE hotel DROP CONSTRAINT FK_3535ED9BF29332C');
        $this->addSql('ALTER TABLE hotel DROP CONSTRAINT FK_3535ED9650760A9');
        $this->addSql('DROP TABLE hotel');
        $this->addSql('ALTER TABLE search DROP from_at');
        $this->addSql('ALTER TABLE search DROP to_at');
        $this->addSql('ALTER TABLE search DROP adults');
        $this->addSql('ALTER TABLE search DROP children');
    }
}
