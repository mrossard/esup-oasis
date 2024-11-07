<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Parametre;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240529113316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout paramètre DESTINATAIRES_COPIE_DECISIONS';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("insert into parametre(id, cle) 
                            values (nextval('parametre_id_seq'), '" . Parametre::DESTINATAIRES_COPIE_DECISIONS . "')");

    }

    public function down(Schema $schema): void
    {

    }
}
