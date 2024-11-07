<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306160406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout support des bénéficiaires non accompagnés';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE beneficiaire ADD avec_accompagnement BOOLEAN DEFAULT true NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE beneficiaire DROP avec_accompagnement');
    }
}
