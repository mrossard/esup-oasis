<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240327094840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE categorie_tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE beneficiaire_tag (beneficiaire_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(beneficiaire_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_2B6ECE655AF81F68 ON beneficiaire_tag (beneficiaire_id)');
        $this->addSql('CREATE INDEX IDX_2B6ECE65BAD26311 ON beneficiaire_tag (tag_id)');
        $this->addSql('CREATE TABLE categorie_tag (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, actif BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tag (id INT NOT NULL, categorie_id INT NOT NULL, libelle VARCHAR(255) NOT NULL, actif BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_389B783BCF5E72D ON tag (categorie_id)');
        $this->addSql('ALTER TABLE beneficiaire_tag ADD CONSTRAINT FK_2B6ECE655AF81F68 FOREIGN KEY (beneficiaire_id) REFERENCES beneficiaire (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE beneficiaire_tag ADD CONSTRAINT FK_2B6ECE65BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie_tag (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE categorie_tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tag_id_seq CASCADE');
        $this->addSql('ALTER TABLE beneficiaire_tag DROP CONSTRAINT FK_2B6ECE655AF81F68');
        $this->addSql('ALTER TABLE beneficiaire_tag DROP CONSTRAINT FK_2B6ECE65BAD26311');
        $this->addSql('ALTER TABLE tag DROP CONSTRAINT FK_389B783BCF5E72D');
        $this->addSql('DROP TABLE beneficiaire_tag');
        $this->addSql('DROP TABLE categorie_tag');
        $this->addSql('DROP TABLE tag');
    }
}
