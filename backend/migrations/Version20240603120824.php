<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Parametre;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20240603120824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout param signature décisions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("insert into parametre(id, cle, fichier)
                            values (nextval('parametre_id_seq'), '" . Parametre::SIGNATURE_DECISIONS . "', true)");

    }

    public function down(Schema $schema): void
    {
    }
}
