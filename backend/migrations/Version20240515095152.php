<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240515095152 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE decision_amenagement_examens_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE decision_amenagement_examens (id INT NOT NULL, beneficiaire_id INT NOT NULL, debut DATE NOT NULL, fin DATE NOT NULL, date_modification DATE NOT NULL, etat VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_72B433E45AF81F68 ON decision_amenagement_examens (beneficiaire_id)');
        $this->addSql('ALTER TABLE decision_amenagement_examens ADD CONSTRAINT FK_72B433E45AF81F68 FOREIGN KEY (beneficiaire_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE decision_amenagement_examens_id_seq CASCADE');
        $this->addSql('ALTER TABLE decision_amenagement_examens DROP CONSTRAINT FK_72B433E45AF81F68');
        $this->addSql('DROP TABLE decision_amenagement_examens');
    }
}
