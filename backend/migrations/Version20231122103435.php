<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231122103435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE intervention_forfait_beneficiaire (intervention_forfait_id INT NOT NULL, beneficiaire_id INT NOT NULL, PRIMARY KEY(intervention_forfait_id, beneficiaire_id))');
        $this->addSql('CREATE INDEX IDX_7CCCCA7D2902521 ON intervention_forfait_beneficiaire (intervention_forfait_id)');
        $this->addSql('CREATE INDEX IDX_7CCCCA75AF81F68 ON intervention_forfait_beneficiaire (beneficiaire_id)');
        $this->addSql('ALTER TABLE intervention_forfait_beneficiaire ADD CONSTRAINT FK_7CCCCA7D2902521 FOREIGN KEY (intervention_forfait_id) REFERENCES intervention_forfait (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervention_forfait_beneficiaire ADD CONSTRAINT FK_7CCCCA75AF81F68 FOREIGN KEY (beneficiaire_id) REFERENCES beneficiaire (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE intervention_forfait_beneficiaire DROP CONSTRAINT FK_7CCCCA7D2902521');
        $this->addSql('ALTER TABLE intervention_forfait_beneficiaire DROP CONSTRAINT FK_7CCCCA75AF81F68');
        $this->addSql('DROP TABLE intervention_forfait_beneficiaire');
    }
}
