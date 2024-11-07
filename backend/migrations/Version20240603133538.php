<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240603133538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'modif de la gestion des PSQS - nom/prénom non fournis';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sportif_haut_niveau ALTER nom DROP NOT NULL');
        $this->addSql('ALTER TABLE sportif_haut_niveau ALTER prenom DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE sportif_haut_niveau ALTER nom SET NOT NULL');
        $this->addSql('ALTER TABLE sportif_haut_niveau ALTER prenom SET NOT NULL');
    }
}
