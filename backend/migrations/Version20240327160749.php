<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\EtatDemande;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240327160749 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'MAJ Etats demandes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("insert into etat_demande(id, libelle, actif)
                            values(" . EtatDemande::ATTENTE_VALIDATION_CHARTE . ", 'Attente validation charte(s)', true)");
        $this->addSql("insert into etat_demande(id, libelle, actif)
                            values(" . EtatDemande::ATTENTE_VALIDATION_ACCOMPAGNEMENT . ", 'Attente validation PHASE', true)");
    }

    public function down(Schema $schema): void
    {

    }
}
