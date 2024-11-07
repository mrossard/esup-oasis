<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240613105547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout champs bilan activité';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE formation ADD niveau VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE formation ADD discipline VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE formation DROP niveau');
        $this->addSql('ALTER TABLE formation DROP discipline');
    }
}
