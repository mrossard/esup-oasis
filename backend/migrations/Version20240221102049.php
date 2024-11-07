<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\EtatDemande;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240221102049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout état demande "Statut validé"';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("insert into etat_demande(id, libelle, actif) 
                            values (" . EtatDemande::PROFIL_VALIDE . ", 'Statut validé', true)");
    }

    public function down(Schema $schema): void
    {

    }
}
