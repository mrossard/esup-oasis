<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\EtatDemande;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240117134416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création des états de demandes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE etat_demande_id_seq CASCADE');

        $this->addSql("insert into etat_demande(id, libelle, actif)
                            values (" . EtatDemande::EN_COURS . ", 'En cours', true)");

    }

    public function down(Schema $schema): void
    {
        $this->addSql('delete from etat_demande where id = ' . EtatDemande::EN_COURS);
    }
}
