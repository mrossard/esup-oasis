<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240603121942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout fichier possible comme valeur de poarametre';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE valeur_parametre ADD fichier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE valeur_parametre ADD CONSTRAINT FK_360B3C16F915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_360B3C16F915CFE ON valeur_parametre (fichier_id)');
        $this->addSql('ALTER TABLE valeur_parametre ALTER valeur DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE valeur_parametre DROP CONSTRAINT FK_360B3C16F915CFE');
        $this->addSql('DROP INDEX IDX_360B3C16F915CFE');
        $this->addSql('ALTER TABLE valeur_parametre DROP fichier_id');
        $this->addSql('ALTER TABLE valeur_parametre ALTER valeur SET NOT NULL');
    }
}
