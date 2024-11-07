<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240530152149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE etablissement_enseignement_artistique_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE etablissement_enseignement_artistique (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, actif BOOLEAN DEFAULT true NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE reponse_discipline_artistique (reponse_id INT NOT NULL, discipline_artistique_id INT NOT NULL, PRIMARY KEY(reponse_id, discipline_artistique_id))');
        $this->addSql('CREATE INDEX IDX_BD73E796CF18BB82 ON reponse_discipline_artistique (reponse_id)');
        $this->addSql('CREATE INDEX IDX_BD73E796F800A0A3 ON reponse_discipline_artistique (discipline_artistique_id)');
        $this->addSql('CREATE TABLE reponse_etablissement_enseignement_artistique (reponse_id INT NOT NULL, etablissement_enseignement_artistique_id INT NOT NULL, PRIMARY KEY(reponse_id, etablissement_enseignement_artistique_id))');
        $this->addSql('CREATE INDEX IDX_FE84BF88CF18BB82 ON reponse_etablissement_enseignement_artistique (reponse_id)');
        $this->addSql('CREATE INDEX IDX_FE84BF88FFF9CCF7 ON reponse_etablissement_enseignement_artistique (etablissement_enseignement_artistique_id)');
        $this->addSql('ALTER TABLE reponse_discipline_artistique ADD CONSTRAINT FK_BD73E796CF18BB82 FOREIGN KEY (reponse_id) REFERENCES reponse (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse_discipline_artistique ADD CONSTRAINT FK_BD73E796F800A0A3 FOREIGN KEY (discipline_artistique_id) REFERENCES discipline_artistique (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse_etablissement_enseignement_artistique ADD CONSTRAINT FK_FE84BF88CF18BB82 FOREIGN KEY (reponse_id) REFERENCES reponse (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse_etablissement_enseignement_artistique ADD CONSTRAINT FK_FE84BF88FFF9CCF7 FOREIGN KEY (etablissement_enseignement_artistique_id) REFERENCES etablissement_enseignement_artistique (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE decision_amenagement_examens ADD fichier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE decision_amenagement_examens ADD CONSTRAINT FK_72B433E4F915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_72B433E4F915CFE ON decision_amenagement_examens (fichier_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE etablissement_enseignement_artistique_id_seq CASCADE');
        $this->addSql('ALTER TABLE reponse_discipline_artistique DROP CONSTRAINT FK_BD73E796CF18BB82');
        $this->addSql('ALTER TABLE reponse_discipline_artistique DROP CONSTRAINT FK_BD73E796F800A0A3');
        $this->addSql('ALTER TABLE reponse_etablissement_enseignement_artistique DROP CONSTRAINT FK_FE84BF88CF18BB82');
        $this->addSql('ALTER TABLE reponse_etablissement_enseignement_artistique DROP CONSTRAINT FK_FE84BF88FFF9CCF7');
        $this->addSql('DROP TABLE etablissement_enseignement_artistique');
        $this->addSql('DROP TABLE reponse_discipline_artistique');
        $this->addSql('DROP TABLE reponse_etablissement_enseignement_artistique');
        $this->addSql('ALTER TABLE decision_amenagement_examens DROP CONSTRAINT FK_72B433E4F915CFE');
        $this->addSql('DROP INDEX UNIQ_72B433E4F915CFE');
        $this->addSql('ALTER TABLE decision_amenagement_examens DROP fichier_id');
    }
}
