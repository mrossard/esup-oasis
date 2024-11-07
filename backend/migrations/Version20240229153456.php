<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240229153456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE membre_commission_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE membre_commission (id INT NOT NULL, commission_id INT NOT NULL, utilisateur_id INT NOT NULL, role VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_46DFBB40202D1EB2 ON membre_commission (commission_id)');
        $this->addSql('CREATE INDEX IDX_46DFBB40FB88E14F ON membre_commission (utilisateur_id)');
        $this->addSql('ALTER TABLE membre_commission ADD CONSTRAINT FK_46DFBB40202D1EB2 FOREIGN KEY (commission_id) REFERENCES commission (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE membre_commission ADD CONSTRAINT FK_46DFBB40FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commission_utilisateur DROP CONSTRAINT fk_c5625c2e202d1eb2');
        $this->addSql('ALTER TABLE commission_utilisateur DROP CONSTRAINT fk_c5625c2efb88e14f');
        $this->addSql('DROP TABLE commission_utilisateur');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE membre_commission_id_seq CASCADE');
        $this->addSql('CREATE TABLE commission_utilisateur (commission_id INT NOT NULL, utilisateur_id INT NOT NULL, PRIMARY KEY(commission_id, utilisateur_id))');
        $this->addSql('CREATE INDEX idx_c5625c2efb88e14f ON commission_utilisateur (utilisateur_id)');
        $this->addSql('CREATE INDEX idx_c5625c2e202d1eb2 ON commission_utilisateur (commission_id)');
        $this->addSql('ALTER TABLE commission_utilisateur ADD CONSTRAINT fk_c5625c2e202d1eb2 FOREIGN KEY (commission_id) REFERENCES commission (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commission_utilisateur ADD CONSTRAINT fk_c5625c2efb88e14f FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE membre_commission DROP CONSTRAINT FK_46DFBB40202D1EB2');
        $this->addSql('ALTER TABLE membre_commission DROP CONSTRAINT FK_46DFBB40FB88E14F');
        $this->addSql('DROP TABLE membre_commission');
    }
}
