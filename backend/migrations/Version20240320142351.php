<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320142351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'ajout lien aménagement => type suivi aménagement';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE amenagement ADD suivi_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE amenagement ADD CONSTRAINT FK_4FF554217FEA59C0 FOREIGN KEY (suivi_id) REFERENCES type_suivi_amenagement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4FF554217FEA59C0 ON amenagement (suivi_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE amenagement DROP CONSTRAINT FK_4FF554217FEA59C0');
        $this->addSql('DROP INDEX IDX_4FF554217FEA59C0');
        $this->addSql('ALTER TABLE amenagement DROP suivi_id');
    }
}
