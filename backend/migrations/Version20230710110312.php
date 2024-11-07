<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230710110312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evenement_utilisateur (evenement_id INT NOT NULL, utilisateur_id INT NOT NULL, PRIMARY KEY(evenement_id, utilisateur_id))');
        $this->addSql('CREATE INDEX IDX_8C897598FD02F13 ON evenement_utilisateur (evenement_id)');
        $this->addSql('CREATE INDEX IDX_8C897598FB88E14F ON evenement_utilisateur (utilisateur_id)');
        $this->addSql('ALTER TABLE evenement_utilisateur ADD CONSTRAINT FK_8C897598FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE evenement_utilisateur ADD CONSTRAINT FK_8C897598FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE evenement_utilisateur DROP CONSTRAINT FK_8C897598FD02F13');
        $this->addSql('ALTER TABLE evenement_utilisateur DROP CONSTRAINT FK_8C897598FB88E14F');
        $this->addSql('DROP TABLE evenement_utilisateur');
    }
}
