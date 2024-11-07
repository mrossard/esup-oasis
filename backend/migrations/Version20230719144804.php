<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Parametre;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230719144804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des paramètres SERVICE_NOM et EXPEDITEUR_EMAILS';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("insert into parametre(id, cle) 
                            values(nextval('parametre_id_seq'), '" . Parametre::EXPEDITEUR_EMAILS . "')");
        $this->addSql("insert into parametre(id, cle) 
                            values(nextval('parametre_id_seq'), '" . Parametre::SERVICE_NOM . "')");

        $this->addSql("insert into valeur_parametre(id, parametre_id, valeur, debut)
                            select nextval('valeur_parametre_id_seq'), id, 'Service PHASE', now()
                            from parametre
                            where cle = '" . Parametre::SERVICE_NOM . "'");
        $this->addSql("insert into valeur_parametre(id, parametre_id, valeur, debut)
                            select nextval('valeur_parametre_id_seq'), id, 'rh.phase@u-bordeaux.fr', now()
                            from parametre
                            where cle = '" . Parametre::EXPEDITEUR_EMAILS . "'");

    }

    public function down(Schema $schema): void
    {

    }
}
