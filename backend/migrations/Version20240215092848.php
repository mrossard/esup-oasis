<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240215092848 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE amenagement_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE amenagement (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, actif BOOLEAN DEFAULT true NOT NULL, pedagogique BOOLEAN NOT NULL, examens BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE reponse_type_engagement (reponse_id INT NOT NULL, type_engagement_id INT NOT NULL, PRIMARY KEY(reponse_id, type_engagement_id))');
        $this->addSql('CREATE INDEX IDX_E43DDDABCF18BB82 ON reponse_type_engagement (reponse_id)');
        $this->addSql('CREATE INDEX IDX_E43DDDABA3E2D8A2 ON reponse_type_engagement (type_engagement_id)');
        $this->addSql('CREATE TABLE reponse_amenagement (reponse_id INT NOT NULL, amenagement_id INT NOT NULL, PRIMARY KEY(reponse_id, amenagement_id))');
        $this->addSql('CREATE INDEX IDX_465E6124CF18BB82 ON reponse_amenagement (reponse_id)');
        $this->addSql('CREATE INDEX IDX_465E61244CCE776B ON reponse_amenagement (amenagement_id)');
        $this->addSql('ALTER TABLE reponse_type_engagement ADD CONSTRAINT FK_E43DDDABCF18BB82 FOREIGN KEY (reponse_id) REFERENCES reponse (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse_type_engagement ADD CONSTRAINT FK_E43DDDABA3E2D8A2 FOREIGN KEY (type_engagement_id) REFERENCES type_engagement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse_amenagement ADD CONSTRAINT FK_465E6124CF18BB82 FOREIGN KEY (reponse_id) REFERENCES reponse (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse_amenagement ADD CONSTRAINT FK_465E61244CCE776B FOREIGN KEY (amenagement_id) REFERENCES amenagement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE amenagement_id_seq CASCADE');
        $this->addSql('ALTER TABLE reponse_type_engagement DROP CONSTRAINT FK_E43DDDABCF18BB82');
        $this->addSql('ALTER TABLE reponse_type_engagement DROP CONSTRAINT FK_E43DDDABA3E2D8A2');
        $this->addSql('ALTER TABLE reponse_amenagement DROP CONSTRAINT FK_465E6124CF18BB82');
        $this->addSql('ALTER TABLE reponse_amenagement DROP CONSTRAINT FK_465E61244CCE776B');
        $this->addSql('DROP TABLE amenagement');
        $this->addSql('DROP TABLE reponse_type_engagement');
        $this->addSql('DROP TABLE reponse_amenagement');
    }
}
