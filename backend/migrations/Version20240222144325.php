<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240222144325 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reponse_fichier (reponse_id INT NOT NULL, fichier_id INT NOT NULL, PRIMARY KEY(reponse_id, fichier_id))');
        $this->addSql('CREATE INDEX IDX_12712A26CF18BB82 ON reponse_fichier (reponse_id)');
        $this->addSql('CREATE INDEX IDX_12712A26F915CFE ON reponse_fichier (fichier_id)');
        $this->addSql('ALTER TABLE reponse_fichier ADD CONSTRAINT FK_12712A26CF18BB82 FOREIGN KEY (reponse_id) REFERENCES reponse (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse_fichier ADD CONSTRAINT FK_12712A26F915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE reponse_fichier DROP CONSTRAINT FK_12712A26CF18BB82');
        $this->addSql('ALTER TABLE reponse_fichier DROP CONSTRAINT FK_12712A26F915CFE');
        $this->addSql('DROP TABLE reponse_fichier');
    }
}
