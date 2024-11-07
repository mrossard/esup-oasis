<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306165722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout lien beneficiaire <=> demande';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE beneficiaire ADD demande_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE beneficiaire ADD CONSTRAINT FK_B140D80280E95E18 FOREIGN KEY (demande_id) REFERENCES demande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B140D80280E95E18 ON beneficiaire (demande_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE beneficiaire DROP CONSTRAINT FK_B140D80280E95E18');
        $this->addSql('DROP INDEX UNIQ_B140D80280E95E18');
        $this->addSql('ALTER TABLE beneficiaire DROP demande_id');
    }
}
