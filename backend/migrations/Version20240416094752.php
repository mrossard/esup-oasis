<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240416094752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reponse_categorie_amenagement (reponse_id INT NOT NULL, categorie_amenagement_id INT NOT NULL, PRIMARY KEY(reponse_id, categorie_amenagement_id))');
        $this->addSql('CREATE INDEX IDX_703B82F8CF18BB82 ON reponse_categorie_amenagement (reponse_id)');
        $this->addSql('CREATE INDEX IDX_703B82F8DAC41004 ON reponse_categorie_amenagement (categorie_amenagement_id)');
        $this->addSql('ALTER TABLE reponse_categorie_amenagement ADD CONSTRAINT FK_703B82F8CF18BB82 FOREIGN KEY (reponse_id) REFERENCES reponse (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse_categorie_amenagement ADD CONSTRAINT FK_703B82F8DAC41004 FOREIGN KEY (categorie_amenagement_id) REFERENCES categorie_amenagement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE reponse_categorie_amenagement DROP CONSTRAINT FK_703B82F8CF18BB82');
        $this->addSql('ALTER TABLE reponse_categorie_amenagement DROP CONSTRAINT FK_703B82F8DAC41004');
        $this->addSql('DROP TABLE reponse_categorie_amenagement');
    }
}
