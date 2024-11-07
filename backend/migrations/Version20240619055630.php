<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Parametre;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240619055630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout parametre ROLES_A_JOUR';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("insert into parametre(id, cle, fichier)
                            values (nextval('parametre_id_seq'), '" . Parametre::ROLES_A_JOUR . "', false)");
        $this->addSql("insert into valeur_parametre(id, parametre_id, valeur, debut) 
                            select nextval('valeur_parametre_id_seq'), id, 'false', now()
                            from parametre
                            where cle = '" . Parametre::ROLES_A_JOUR . "'");
    }

    public function down(Schema $schema): void
    {

    }
}
