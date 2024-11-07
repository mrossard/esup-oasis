<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240314155258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE amenagement_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE amenagement (id INT NOT NULL, type_id INT NOT NULL, semestre1 BOOLEAN NOT NULL, semestre2 BOOLEAN NOT NULL, debut DATE DEFAULT NULL, fin DATE DEFAULT NULL, commentaire TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4FF55421C54C8C93 ON amenagement (type_id)');
        $this->addSql('CREATE TABLE amenagement_beneficiaire (amenagement_id INT NOT NULL, beneficiaire_id INT NOT NULL, PRIMARY KEY(amenagement_id, beneficiaire_id))');
        $this->addSql('CREATE INDEX IDX_2DABDEAB4CCE776B ON amenagement_beneficiaire (amenagement_id)');
        $this->addSql('CREATE INDEX IDX_2DABDEAB5AF81F68 ON amenagement_beneficiaire (beneficiaire_id)');
        $this->addSql('ALTER TABLE amenagement ADD CONSTRAINT FK_4FF55421C54C8C93 FOREIGN KEY (type_id) REFERENCES type_amenagement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE amenagement_beneficiaire ADD CONSTRAINT FK_2DABDEAB4CCE776B FOREIGN KEY (amenagement_id) REFERENCES amenagement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE amenagement_beneficiaire ADD CONSTRAINT FK_2DABDEAB5AF81F68 FOREIGN KEY (beneficiaire_id) REFERENCES beneficiaire (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE amenagement_id_seq CASCADE');
        $this->addSql('ALTER TABLE amenagement DROP CONSTRAINT FK_4FF55421C54C8C93');
        $this->addSql('ALTER TABLE amenagement_beneficiaire DROP CONSTRAINT FK_2DABDEAB4CCE776B');
        $this->addSql('ALTER TABLE amenagement_beneficiaire DROP CONSTRAINT FK_2DABDEAB5AF81F68');
        $this->addSql('DROP TABLE amenagement');
        $this->addSql('DROP TABLE amenagement_beneficiaire');
    }
}
