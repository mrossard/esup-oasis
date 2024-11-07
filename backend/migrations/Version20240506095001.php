<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240506095001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE parametre_ui_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE parametre_ui (id INT NOT NULL, utilisateur_id INT NOT NULL, cle VARCHAR(255) NOT NULL, valeur TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FB9EDE82FB88E14F ON parametre_ui (utilisateur_id)');
        $this->addSql('ALTER TABLE parametre_ui ADD CONSTRAINT FK_FB9EDE82FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE parametre_ui_id_seq CASCADE');
        $this->addSql('ALTER TABLE parametre_ui DROP CONSTRAINT FK_FB9EDE82FB88E14F');
        $this->addSql('DROP TABLE parametre_ui');
    }
}
