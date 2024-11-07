<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230726000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout abonnements';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur ADD abonne_immediat BOOLEAN NOT NULL DEFAULT false');
        $this->addSql('ALTER TABLE utilisateur ADD abonne_veille BOOLEAN NOT NULL DEFAULT false');
        $this->addSql('ALTER TABLE utilisateur ADD abonne_avant_veille BOOLEAN NOT NULL DEFAULT false');
        $this->addSql('ALTER TABLE utilisateur ADD abonne_recap_hebdo BOOLEAN NOT NULL DEFAULT false');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE utilisateur DROP abonne_immediat');
        $this->addSql('ALTER TABLE utilisateur DROP abonne_veille');
        $this->addSql('ALTER TABLE utilisateur DROP abonne_avant_veille');
        $this->addSql('ALTER TABLE utilisateur DROP abonne_recap_hebdo');
    }
}
