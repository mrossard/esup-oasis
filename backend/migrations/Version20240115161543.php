<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240115161543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE commission ADD actif BOOLEAN NOT NULL DEFAULT true');
        $this->addSql('ALTER TABLE type_demande ADD actif BOOLEAN NOT NULL default true');
        $this->addSql('ALTER TABLE question ADD obligatoire BOOLEAN NOT NULL default false');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE commission DROP actif');
        $this->addSql('ALTER TABLE type_demande DROP actif');
        $this->addSql('ALTER TABLE question DROP obligatoire');
    }
}
