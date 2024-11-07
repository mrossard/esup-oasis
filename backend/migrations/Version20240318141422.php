<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240318141422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return "Ajout des catégories d'aménagement";
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE categorie_amenagement_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE categorie_amenagement (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, actif BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE type_amenagement ADD categorie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE type_amenagement ADD CONSTRAINT FK_4EC83B0BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie_amenagement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4EC83B0BCF5E72D ON type_amenagement (categorie_id)');

        $this->populate();
    }

    public function populate(): void
    {
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Adaptation emploi du temps', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Etalement / Fractionnement', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Adaptation emploi du temps', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Régime de dispense', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Informations spécifiques enseignants', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Prise de notes', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Secrétariat d''épreuve', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Tutorat', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Autre aide humaine', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Adaptation des documents', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Aménagement du temps d''épreuve', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Utilisation d''un ordinateur', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Utilisation d''un ordinateur', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Documents adaptés', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Autorisation de sortie', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Salle particulière', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Documents adaptés', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Secrétaire d''épreuve', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Accessibilité des locaux', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Installation particulière dans la salle', true)");
        $this->addSql("insert into categorie_amenagement(id, libelle, actif)
                            values(nextval('categorie_amenagement_id_seq'), 'Autre', true)");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE type_amenagement DROP CONSTRAINT FK_4EC83B0BCF5E72D');
        $this->addSql('DROP SEQUENCE categorie_amenagement_id_seq CASCADE');
        $this->addSql('DROP TABLE categorie_amenagement');
        $this->addSql('DROP INDEX IDX_4EC83B0BCF5E72D');
        $this->addSql('ALTER TABLE type_amenagement DROP categorie_id');
    }
}
