<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240213143416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE discipline_sportive_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE discipline_sportive (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, actif BOOLEAN DEFAULT true NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE question ADD table_options VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE discipline_sportive_id_seq CASCADE');
        $this->addSql('DROP TABLE discipline_sportive');
        $this->addSql('ALTER TABLE question DROP table_options');
    }
}
