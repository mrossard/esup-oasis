<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240321135130 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE charte_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE charte (id INT NOT NULL, contenu TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE charte_profil_beneficiaire (charte_id INT NOT NULL, profil_beneficiaire_id INT NOT NULL, PRIMARY KEY(charte_id, profil_beneficiaire_id))');
        $this->addSql('CREATE INDEX IDX_6EBA71907268A446 ON charte_profil_beneficiaire (charte_id)');
        $this->addSql('CREATE INDEX IDX_6EBA719018607C3D ON charte_profil_beneficiaire (profil_beneficiaire_id)');
        $this->addSql('ALTER TABLE charte_profil_beneficiaire ADD CONSTRAINT FK_6EBA71907268A446 FOREIGN KEY (charte_id) REFERENCES charte (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE charte_profil_beneficiaire ADD CONSTRAINT FK_6EBA719018607C3D FOREIGN KEY (profil_beneficiaire_id) REFERENCES profil_beneficiaire (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE charte_id_seq CASCADE');
        $this->addSql('ALTER TABLE charte_profil_beneficiaire DROP CONSTRAINT FK_6EBA71907268A446');
        $this->addSql('ALTER TABLE charte_profil_beneficiaire DROP CONSTRAINT FK_6EBA719018607C3D');
        $this->addSql('DROP TABLE charte');
        $this->addSql('DROP TABLE charte_profil_beneficiaire');
    }
}
