<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240528120035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout PJ Bénéficiaires';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE piece_jointe_beneficiaire_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE piece_jointe_beneficiaire (id INT NOT NULL, fichier_id INT NOT NULL, utilisateur_creation_id INT NOT NULL, beneficiaire_id INT NOT NULL, libelle VARCHAR(255) NOT NULL, date_depot DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FD77900AF915CFE ON piece_jointe_beneficiaire (fichier_id)');
        $this->addSql('CREATE INDEX IDX_FD77900AC019AB09 ON piece_jointe_beneficiaire (utilisateur_creation_id)');
        $this->addSql('CREATE INDEX IDX_FD77900A5AF81F68 ON piece_jointe_beneficiaire (beneficiaire_id)');
        $this->addSql('ALTER TABLE piece_jointe_beneficiaire ADD CONSTRAINT FK_FD77900AF915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE piece_jointe_beneficiaire ADD CONSTRAINT FK_FD77900AC019AB09 FOREIGN KEY (utilisateur_creation_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE piece_jointe_beneficiaire ADD CONSTRAINT FK_FD77900A5AF81F68 FOREIGN KEY (beneficiaire_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE question ADD champ_cible VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE piece_jointe_beneficiaire_id_seq CASCADE');
        $this->addSql('ALTER TABLE piece_jointe_beneficiaire DROP CONSTRAINT FK_FD77900AF915CFE');
        $this->addSql('ALTER TABLE piece_jointe_beneficiaire DROP CONSTRAINT FK_FD77900AC019AB09');
        $this->addSql('ALTER TABLE piece_jointe_beneficiaire DROP CONSTRAINT FK_FD77900A5AF81F68');
        $this->addSql('DROP TABLE piece_jointe_beneficiaire');
        $this->addSql('ALTER TABLE question DROP champ_cible');
    }
}
