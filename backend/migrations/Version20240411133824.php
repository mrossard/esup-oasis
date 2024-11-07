<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411133824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE entretien_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE entretien (id INT NOT NULL, fichier_id INT DEFAULT NULL, commentaire TEXT DEFAULT NULL, date DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2B58D6DAF915CFE ON entretien (fichier_id)');
        $this->addSql('ALTER TABLE entretien ADD CONSTRAINT FK_2B58D6DAF915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE entretien_id_seq CASCADE');
        $this->addSql('ALTER TABLE entretien DROP CONSTRAINT FK_2B58D6DAF915CFE');
        $this->addSql('DROP TABLE entretien');
    }
}
