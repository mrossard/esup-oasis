<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230704083040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intervention_forfait ADD utilisateur_creation_id INT NOT NULL');
        $this->addSql('ALTER TABLE intervention_forfait ADD utilisateur_modification_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE intervention_forfait ADD date_creation DATE NOT NULL');
        $this->addSql('ALTER TABLE intervention_forfait ADD date_modification DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE intervention_forfait ADD CONSTRAINT FK_F05BB0E7C019AB09 FOREIGN KEY (utilisateur_creation_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervention_forfait ADD CONSTRAINT FK_F05BB0E75FB9EE51 FOREIGN KEY (utilisateur_modification_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F05BB0E7C019AB09 ON intervention_forfait (utilisateur_creation_id)');
        $this->addSql('CREATE INDEX IDX_F05BB0E75FB9EE51 ON intervention_forfait (utilisateur_modification_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE intervention_forfait DROP CONSTRAINT FK_F05BB0E7C019AB09');
        $this->addSql('ALTER TABLE intervention_forfait DROP CONSTRAINT FK_F05BB0E75FB9EE51');
        $this->addSql('DROP INDEX IDX_F05BB0E7C019AB09');
        $this->addSql('DROP INDEX IDX_F05BB0E75FB9EE51');
        $this->addSql('ALTER TABLE intervention_forfait DROP utilisateur_creation_id');
        $this->addSql('ALTER TABLE intervention_forfait DROP utilisateur_modification_id');
        $this->addSql('ALTER TABLE intervention_forfait DROP date_creation');
        $this->addSql('ALTER TABLE intervention_forfait DROP date_modification');
    }
}
