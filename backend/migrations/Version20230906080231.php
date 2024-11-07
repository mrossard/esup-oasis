<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Parametre;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230906080231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout paramètre RAPPELS_SANS_INTERVENANTS';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("insert into parametre(id, cle) 
                            values(nextval('parametre_id_seq'), '" . Parametre::RAPPELS_SANS_INTERVENANTS . "')");

        $this->addSql("insert into valeur_parametre(id, parametre_id, valeur, debut)
                            select nextval('valeur_parametre_id_seq'), id, 'O', now()
                            from parametre
                            where cle = '" . Parametre::RAPPELS_SANS_INTERVENANTS . "'");

    }

    public function down(Schema $schema): void
    {

    }
}
