<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240219104307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des clubs sportifs';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE club_sportif_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE club_sportif (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, centre_formation BOOLEAN DEFAULT false NOT NULL, professionnel BOOLEAN DEFAULT false NOT NULL, actif BOOLEAN DEFAULT true NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE reponse_club_sportif (reponse_id INT NOT NULL, club_sportif_id INT NOT NULL, PRIMARY KEY(reponse_id, club_sportif_id))');
        $this->addSql('CREATE INDEX IDX_AFA7BEB5CF18BB82 ON reponse_club_sportif (reponse_id)');
        $this->addSql('CREATE INDEX IDX_AFA7BEB5B67146D6 ON reponse_club_sportif (club_sportif_id)');
        $this->addSql('ALTER TABLE reponse_club_sportif ADD CONSTRAINT FK_AFA7BEB5CF18BB82 FOREIGN KEY (reponse_id) REFERENCES reponse (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse_club_sportif ADD CONSTRAINT FK_AFA7BEB5B67146D6 FOREIGN KEY (club_sportif_id) REFERENCES club_sportif (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE club_sportif_id_seq CASCADE');
        $this->addSql('ALTER TABLE reponse_club_sportif DROP CONSTRAINT FK_AFA7BEB5CF18BB82');
        $this->addSql('ALTER TABLE reponse_club_sportif DROP CONSTRAINT FK_AFA7BEB5B67146D6');
        $this->addSql('DROP TABLE club_sportif');
        $this->addSql('DROP TABLE reponse_club_sportif');
    }
}
