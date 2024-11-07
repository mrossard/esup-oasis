<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240313144655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE modification_etat_demande_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE modification_etat_demande (id INT NOT NULL, demande_id INT NOT NULL, etat_id INT NOT NULL, etat_precedent_id INT DEFAULT NULL, utilisateur_id INT NOT NULL, profil_id INT DEFAULT NULL, commentaire TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_14DA320A80E95E18 ON modification_etat_demande (demande_id)');
        $this->addSql('CREATE INDEX IDX_14DA320AD5E86FF ON modification_etat_demande (etat_id)');
        $this->addSql('CREATE INDEX IDX_14DA320A57BF5726 ON modification_etat_demande (etat_precedent_id)');
        $this->addSql('CREATE INDEX IDX_14DA320AFB88E14F ON modification_etat_demande (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_14DA320A275ED078 ON modification_etat_demande (profil_id)');
        $this->addSql('ALTER TABLE modification_etat_demande ADD CONSTRAINT FK_14DA320A80E95E18 FOREIGN KEY (demande_id) REFERENCES demande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE modification_etat_demande ADD CONSTRAINT FK_14DA320AD5E86FF FOREIGN KEY (etat_id) REFERENCES etat_demande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE modification_etat_demande ADD CONSTRAINT FK_14DA320A57BF5726 FOREIGN KEY (etat_precedent_id) REFERENCES etat_demande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE modification_etat_demande ADD CONSTRAINT FK_14DA320AFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE modification_etat_demande ADD CONSTRAINT FK_14DA320A275ED078 FOREIGN KEY (profil_id) REFERENCES profil_beneficiaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE modification_etat_demande_id_seq CASCADE');
        $this->addSql('ALTER TABLE modification_etat_demande DROP CONSTRAINT FK_14DA320A80E95E18');
        $this->addSql('ALTER TABLE modification_etat_demande DROP CONSTRAINT FK_14DA320AD5E86FF');
        $this->addSql('ALTER TABLE modification_etat_demande DROP CONSTRAINT FK_14DA320A57BF5726');
        $this->addSql('ALTER TABLE modification_etat_demande DROP CONSTRAINT FK_14DA320AFB88E14F');
        $this->addSql('ALTER TABLE modification_etat_demande DROP CONSTRAINT FK_14DA320A275ED078');
        $this->addSql('DROP TABLE modification_etat_demande');
    }
}
