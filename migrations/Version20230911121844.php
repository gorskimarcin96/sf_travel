<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230911121844 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE trip_article_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE trip_page_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE trip_article (id INT NOT NULL, page_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, descriptions JSON NOT NULL, images JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_34D64BCBC4663E4 ON trip_article (page_id)');
        $this->addSql('CREATE TABLE trip_page (id INT NOT NULL, url VARCHAR(255) NOT NULL, map VARCHAR(255) DEFAULT NULL, source VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE trip_article ADD CONSTRAINT FK_34D64BCBC4663E4 FOREIGN KEY (page_id) REFERENCES trip_page (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE trip_article_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE trip_page_id_seq CASCADE');
        $this->addSql('ALTER TABLE trip_article DROP CONSTRAINT FK_34D64BCBC4663E4');
        $this->addSql('DROP TABLE trip_article');
        $this->addSql('DROP TABLE trip_page');
    }
}
