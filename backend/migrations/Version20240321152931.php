<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240321152931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE charte_demandeur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE charte_demandeur (id INT NOT NULL, demande_id INT NOT NULL, charte_id INT NOT NULL, date_validation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, contenu TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_747BB35D80E95E18 ON charte_demandeur (demande_id)');
        $this->addSql('CREATE INDEX IDX_747BB35D7268A446 ON charte_demandeur (charte_id)');
        $this->addSql('ALTER TABLE charte_demandeur ADD CONSTRAINT FK_747BB35D80E95E18 FOREIGN KEY (demande_id) REFERENCES demande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE charte_demandeur ADD CONSTRAINT FK_747BB35D7268A446 FOREIGN KEY (charte_id) REFERENCES charte (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE charte ADD libelle VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE charte_demandeur_id_seq CASCADE');
        $this->addSql('ALTER TABLE charte_demandeur DROP CONSTRAINT FK_747BB35D80E95E18');
        $this->addSql('ALTER TABLE charte_demandeur DROP CONSTRAINT FK_747BB35D7268A446');
        $this->addSql('DROP TABLE charte_demandeur');
        $this->addSql('ALTER TABLE charte DROP libelle');
    }
}
