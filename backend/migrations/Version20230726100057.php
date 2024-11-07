<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Parametre;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20230726100057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout paramètre FREQUENCE_RAPPELS';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("insert into parametre(id, cle) 
                            values(nextval('parametre_id_seq'), '" . Parametre::FREQUENCE_RAPPELS . "')");

        $this->addSql("insert into valeur_parametre(id, parametre_id, valeur, debut)
                            select nextval('valeur_parametre_id_seq'), id, '7 days', now()
                            from parametre
                            where cle = '" . Parametre::FREQUENCE_RAPPELS . "'");

    }

    public function down(Schema $schema): void
    {

    }
}
