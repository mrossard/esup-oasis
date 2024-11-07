<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240312134750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout états demandes manquants';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("insert into etat_demande(id, libelle, actif)
                            values(7, 'Avis commission demandé', true)");
        $this->addSql("insert into etat_demande(id, libelle, actif)
                            values(8, 'Non conforme', true)");
    }

    public function down(Schema $schema): void
    {
    }
}
