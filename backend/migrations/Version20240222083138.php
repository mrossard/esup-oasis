<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240222083138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE fichier_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE fichier (id INT NOT NULL, proprietaire_id INT DEFAULT NULL, metadata JSON NOT NULL, nom VARCHAR(255) NOT NULL, type_mime VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9B76551F76C50E4A ON fichier (proprietaire_id)');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551F76C50E4A FOREIGN KEY (proprietaire_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE fichier_id_seq CASCADE');
        $this->addSql('ALTER TABLE fichier DROP CONSTRAINT FK_9B76551F76C50E4A');
        $this->addSql('DROP TABLE fichier');
    }
}
