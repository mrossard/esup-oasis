<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240214162101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reponse_typologie_handicap (reponse_id INT NOT NULL, typologie_handicap_id INT NOT NULL, PRIMARY KEY(reponse_id, typologie_handicap_id))');
        $this->addSql('CREATE INDEX IDX_277B736ACF18BB82 ON reponse_typologie_handicap (reponse_id)');
        $this->addSql('CREATE INDEX IDX_277B736AF4F9B0 ON reponse_typologie_handicap (typologie_handicap_id)');
        $this->addSql('ALTER TABLE reponse_typologie_handicap ADD CONSTRAINT FK_277B736ACF18BB82 FOREIGN KEY (reponse_id) REFERENCES reponse (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse_typologie_handicap ADD CONSTRAINT FK_277B736AF4F9B0 FOREIGN KEY (typologie_handicap_id) REFERENCES typologie_handicap (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE reponse_typologie_handicap DROP CONSTRAINT FK_277B736ACF18BB82');
        $this->addSql('ALTER TABLE reponse_typologie_handicap DROP CONSTRAINT FK_277B736AF4F9B0');
        $this->addSql('DROP TABLE reponse_typologie_handicap');
    }
}
