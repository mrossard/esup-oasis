<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240212143445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reponse_option_reponse (reponse_id INT NOT NULL, option_reponse_id INT NOT NULL, PRIMARY KEY(reponse_id, option_reponse_id))');
        $this->addSql('CREATE INDEX IDX_A2BCB386CF18BB82 ON reponse_option_reponse (reponse_id)');
        $this->addSql('CREATE INDEX IDX_A2BCB3862271A3B2 ON reponse_option_reponse (option_reponse_id)');
        $this->addSql('ALTER TABLE reponse_option_reponse ADD CONSTRAINT FK_A2BCB386CF18BB82 FOREIGN KEY (reponse_id) REFERENCES reponse (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse_option_reponse ADD CONSTRAINT FK_A2BCB3862271A3B2 FOREIGN KEY (option_reponse_id) REFERENCES option_reponse (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse DROP CONSTRAINT fk_5fb6dec7480126e8');
        $this->addSql('DROP INDEX idx_5fb6dec7480126e8');
        $this->addSql('ALTER TABLE reponse DROP option_choisie_id');
        $this->addSql('ALTER TABLE question ADD choix_multiple BOOLEAN DEFAULT false NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE question DROP choix_multiple');
        $this->addSql('ALTER TABLE reponse_option_reponse DROP CONSTRAINT FK_A2BCB386CF18BB82');
        $this->addSql('ALTER TABLE reponse_option_reponse DROP CONSTRAINT FK_A2BCB3862271A3B2');
        $this->addSql('DROP TABLE reponse_option_reponse');
        $this->addSql('ALTER TABLE reponse ADD option_choisie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT fk_5fb6dec7480126e8 FOREIGN KEY (option_choisie_id) REFERENCES option_reponse (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_5fb6dec7480126e8 ON reponse (option_choisie_id)');
    }
}
