<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240214090627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reponse_discipline_sportive (reponse_id INT NOT NULL, discipline_sportive_id INT NOT NULL, PRIMARY KEY(reponse_id, discipline_sportive_id))');
        $this->addSql('CREATE INDEX IDX_9804C9A0CF18BB82 ON reponse_discipline_sportive (reponse_id)');
        $this->addSql('CREATE INDEX IDX_9804C9A0245FD2F2 ON reponse_discipline_sportive (discipline_sportive_id)');
        $this->addSql('ALTER TABLE reponse_discipline_sportive ADD CONSTRAINT FK_9804C9A0CF18BB82 FOREIGN KEY (reponse_id) REFERENCES reponse (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse_discipline_sportive ADD CONSTRAINT FK_9804C9A0245FD2F2 FOREIGN KEY (discipline_sportive_id) REFERENCES discipline_sportive (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE reponse_discipline_sportive DROP CONSTRAINT FK_9804C9A0CF18BB82');
        $this->addSql('ALTER TABLE reponse_discipline_sportive DROP CONSTRAINT FK_9804C9A0245FD2F2');
        $this->addSql('DROP TABLE reponse_discipline_sportive');
    }
}
