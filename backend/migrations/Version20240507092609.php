<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240507092609 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE composante_utilisateur (composante_id INT NOT NULL, utilisateur_id INT NOT NULL, PRIMARY KEY(composante_id, utilisateur_id))');
        $this->addSql('CREATE INDEX IDX_DBB2E75BAC12F1AD ON composante_utilisateur (composante_id)');
        $this->addSql('CREATE INDEX IDX_DBB2E75BFB88E14F ON composante_utilisateur (utilisateur_id)');
        $this->addSql('ALTER TABLE composante_utilisateur ADD CONSTRAINT FK_DBB2E75BAC12F1AD FOREIGN KEY (composante_id) REFERENCES composante (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE composante_utilisateur ADD CONSTRAINT FK_DBB2E75BFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE composante_utilisateur DROP CONSTRAINT FK_DBB2E75BAC12F1AD');
        $this->addSql('ALTER TABLE composante_utilisateur DROP CONSTRAINT FK_DBB2E75BFB88E14F');
        $this->addSql('DROP TABLE composante_utilisateur');
    }
}
