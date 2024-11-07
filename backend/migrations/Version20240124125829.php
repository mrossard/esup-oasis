<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\EtatDemande;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240124125829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout états des demandes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("insert into etat_demande(id, libelle, actif)
                            values(" . EtatDemande::RECEPTIONNEE . ", 'Réceptionnée', true)");
        $this->addSql("insert into etat_demande(id, libelle, actif)
                            values(" . EtatDemande::CONFORME . ", 'Conforme', true)");
        $this->addSql("insert into etat_demande(id, libelle, actif)
                            values(" . EtatDemande::VALIDEE . ", 'Validée', true)");
        $this->addSql("insert into etat_demande(id, libelle, actif)
                            values(" . EtatDemande::REFUSEE . ", 'Refusée', true)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("delete from etat_demande 
                            where id in(" . EtatDemande::RECEPTIONNEE . ", " .
            EtatDemande::CONFORME . ", " .
            EtatDemande::VALIDEE . ", " .
            EtatDemande::REFUSEE . " )");
    }
}
