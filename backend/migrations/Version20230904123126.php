<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20230904123126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout parametre FREQUENCE_RAPPEL_RENFORTS';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("insert into parametre(id, cle) 
                            values(nextval('parametre_id_seq'), 'FREQUENCE_RAPPEL_RENFORTS')");

        $this->addSql("insert into valeur_parametre(id, parametre_id, valeur, debut)
                            select nextval('valeur_parametre_id_seq'), id, '1 day', now()
                            from parametre
                            where cle = 'FREQUENCE_RAPPEL_RENFORTS'");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
    }
}
