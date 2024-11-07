<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240624130818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE bilan_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE bilan (id INT NOT NULL, fichier_id INT DEFAULT NULL, demandeur_id INT NOT NULL, date_demande TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_generation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, debut DATE NOT NULL, fin DATE NOT NULL, parametres JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F4DF4F44F915CFE ON bilan (fichier_id)');
        $this->addSql('CREATE INDEX IDX_F4DF4F4495A6EE59 ON bilan (demandeur_id)');
        $this->addSql('ALTER TABLE bilan ADD CONSTRAINT FK_F4DF4F44F915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bilan ADD CONSTRAINT FK_F4DF4F4495A6EE59 FOREIGN KEY (demandeur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE bilan_id_seq CASCADE');
        $this->addSql('ALTER TABLE bilan DROP CONSTRAINT FK_F4DF4F44F915CFE');
        $this->addSql('ALTER TABLE bilan DROP CONSTRAINT FK_F4DF4F4495A6EE59');
        $this->addSql('DROP TABLE bilan');
    }
}
