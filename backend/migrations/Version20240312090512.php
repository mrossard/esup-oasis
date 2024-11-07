<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240312090512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE amenagement_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE type_amenagement_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE reponse_type_amenagement (reponse_id INT NOT NULL, type_amenagement_id INT NOT NULL, PRIMARY KEY(reponse_id, type_amenagement_id))');
        $this->addSql('CREATE INDEX IDX_F160D9BCF18BB82 ON reponse_type_amenagement (reponse_id)');
        $this->addSql('CREATE INDEX IDX_F160D9B1A0D5EFF ON reponse_type_amenagement (type_amenagement_id)');
        $this->addSql('CREATE TABLE type_amenagement (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, actif BOOLEAN DEFAULT true NOT NULL, pedagogique BOOLEAN DEFAULT false NOT NULL, examens BOOLEAN DEFAULT false NOT NULL, aide_humaine BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE reponse_type_amenagement ADD CONSTRAINT FK_F160D9BCF18BB82 FOREIGN KEY (reponse_id) REFERENCES reponse (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse_type_amenagement ADD CONSTRAINT FK_F160D9B1A0D5EFF FOREIGN KEY (type_amenagement_id) REFERENCES type_amenagement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse_amenagement DROP CONSTRAINT fk_465e6124cf18bb82');
        $this->addSql('ALTER TABLE reponse_amenagement DROP CONSTRAINT fk_465e61244cce776b');
        $this->addSql('DROP TABLE amenagement');
        $this->addSql('DROP TABLE reponse_amenagement');
        $this->addSql('ALTER TABLE membre_commission ALTER roles DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE type_amenagement_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE amenagement_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE amenagement (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, actif BOOLEAN DEFAULT true NOT NULL, pedagogique BOOLEAN NOT NULL, examens BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE reponse_amenagement (reponse_id INT NOT NULL, amenagement_id INT NOT NULL, PRIMARY KEY(reponse_id, amenagement_id))');
        $this->addSql('CREATE INDEX idx_465e61244cce776b ON reponse_amenagement (amenagement_id)');
        $this->addSql('CREATE INDEX idx_465e6124cf18bb82 ON reponse_amenagement (reponse_id)');
        $this->addSql('ALTER TABLE reponse_amenagement ADD CONSTRAINT fk_465e6124cf18bb82 FOREIGN KEY (reponse_id) REFERENCES reponse (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse_amenagement ADD CONSTRAINT fk_465e61244cce776b FOREIGN KEY (amenagement_id) REFERENCES amenagement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse_type_amenagement DROP CONSTRAINT FK_F160D9BCF18BB82');
        $this->addSql('ALTER TABLE reponse_type_amenagement DROP CONSTRAINT FK_F160D9B1A0D5EFF');
        $this->addSql('DROP TABLE reponse_type_amenagement');
        $this->addSql('DROP TABLE type_amenagement');
        $this->addSql('ALTER TABLE membre_commission ALTER roles SET DEFAULT \'{}\'');
    }
}
