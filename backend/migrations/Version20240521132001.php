<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240521132001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout paramètres pour conf édition des décisions';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("insert into parametre(id, cle) 
                            values (nextval('parametre_id_seq'), 'PRESIDENT_QUALITE')");

        $this->addSql("insert into valeur_parametre(id, parametre_id, valeur, debut)
                            select nextval('valeur_parametre_id_seq'), id, 'Le président de l''université', now()
                            from parametre
                            where cle = 'PRESIDENT_QUALITE'");

        $this->addSql("insert into parametre(id, cle) 
                            values (nextval('parametre_id_seq'), 'PRESIDENT_NOM')");

        $this->addSql("insert into valeur_parametre(id, parametre_id, valeur, debut)
                            select nextval('valeur_parametre_id_seq'), id, 'Dean LEWIS', now()
                            from parametre
                            where cle = 'PRESIDENT_NOM'");

        $this->addSql("insert into parametre(id, cle) 
                            values (nextval('parametre_id_seq'), 'RESPONSABLE_PHASE_QUALITE')");

        $this->addSql("insert into valeur_parametre(id, parametre_id, valeur, debut)
                            select nextval('valeur_parametre_id_seq'), id, 'la responsable du Service PHASE', now()
                            from parametre
                            where cle = 'RESPONSABLE_PHASE_QUALITE'");

        $this->addSql("insert into parametre(id, cle) 
                            values (nextval('parametre_id_seq'), 'RESPONSABLE_PHASE_NOM')");

        $this->addSql("insert into valeur_parametre(id, parametre_id, valeur, debut)
                            select nextval('valeur_parametre_id_seq'), id, 'Karine Verdeau', now()
                            from parametre
                            where cle = 'RESPONSABLE_PHASE_NOM'");

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
    }
}
