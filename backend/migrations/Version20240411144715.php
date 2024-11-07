<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240411144715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entretien ADD utilisateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE entretien ADD gestionnaire_id INT NOT NULL');
        $this->addSql('ALTER TABLE entretien ADD CONSTRAINT FK_2B58D6DAFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE entretien ADD CONSTRAINT FK_2B58D6DA6885AC1B FOREIGN KEY (gestionnaire_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2B58D6DAFB88E14F ON entretien (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_2B58D6DA6885AC1B ON entretien (gestionnaire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE entretien DROP CONSTRAINT FK_2B58D6DAFB88E14F');
        $this->addSql('ALTER TABLE entretien DROP CONSTRAINT FK_2B58D6DA6885AC1B');
        $this->addSql('DROP INDEX IDX_2B58D6DAFB88E14F');
        $this->addSql('DROP INDEX IDX_2B58D6DA6885AC1B');
        $this->addSql('ALTER TABLE entretien DROP utilisateur_id');
        $this->addSql('ALTER TABLE entretien DROP gestionnaire_id');
    }
}
