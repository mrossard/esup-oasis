<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20230620082004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Init de la base';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE beneficiaire_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE campus_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE competence_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE composante_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE evenement_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE formation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE inscription_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE intervenant_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE parametre_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE periode_rh_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE profil_beneficiaire_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE service_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE taux_horaire_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE type_equipement_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE type_evenement_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE utilisateur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE beneficiaire (id INT NOT NULL, profil_id INT NOT NULL, utilisateur_id INT NOT NULL, gestionnaire_id INT NOT NULL, debut DATE NOT NULL, fin DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B140D802275ED078 ON beneficiaire (profil_id)');
        $this->addSql('CREATE INDEX IDX_B140D802FB88E14F ON beneficiaire (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_B140D8026885AC1B ON beneficiaire (gestionnaire_id)');
        $this->addSql('CREATE TABLE campus (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, actif BOOLEAN DEFAULT true NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE competence (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, actif BOOLEAN DEFAULT true NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE composante (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, code_externe VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE evenement (id INT NOT NULL, type_id INT NOT NULL, campus_id INT NOT NULL, intervenant_id INT DEFAULT NULL, periode_prise_en_compte_rh_id INT DEFAULT NULL, utilisateur_creation_id INT NOT NULL, utilisateur_modification_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, salle VARCHAR(255) DEFAULT NULL, debut TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, fin TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, temps_preparation INT DEFAULT 0 NOT NULL, temps_supplementaire INT DEFAULT 0 NOT NULL, date_annulation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_validation TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, date_creation DATE NOT NULL, date_modification DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B26681EC54C8C93 ON evenement (type_id)');
        $this->addSql('CREATE INDEX IDX_B26681EAF5D55E1 ON evenement (campus_id)');
        $this->addSql('CREATE INDEX IDX_B26681EAB9A1716 ON evenement (intervenant_id)');
        $this->addSql('CREATE INDEX IDX_B26681EAC8EDCFF ON evenement (periode_prise_en_compte_rh_id)');
        $this->addSql('CREATE INDEX IDX_B26681EC019AB09 ON evenement (utilisateur_creation_id)');
        $this->addSql('CREATE INDEX IDX_B26681E5FB9EE51 ON evenement (utilisateur_modification_id)');
        $this->addSql('CREATE TABLE evenement_type_equipement (evenement_id INT NOT NULL, type_equipement_id INT NOT NULL, PRIMARY KEY(evenement_id, type_equipement_id))');
        $this->addSql('CREATE INDEX IDX_41113AA6FD02F13 ON evenement_type_equipement (evenement_id)');
        $this->addSql('CREATE INDEX IDX_41113AA6F082B869 ON evenement_type_equipement (type_equipement_id)');
        $this->addSql('CREATE TABLE evenement_beneficiaire (evenement_id INT NOT NULL, beneficiaire_id INT NOT NULL, PRIMARY KEY(evenement_id, beneficiaire_id))');
        $this->addSql('CREATE INDEX IDX_1D6DB454FD02F13 ON evenement_beneficiaire (evenement_id)');
        $this->addSql('CREATE INDEX IDX_1D6DB4545AF81F68 ON evenement_beneficiaire (beneficiaire_id)');
        $this->addSql('CREATE TABLE evenement_intervenant (evenement_id INT NOT NULL, intervenant_id INT NOT NULL, PRIMARY KEY(evenement_id, intervenant_id))');
        $this->addSql('CREATE INDEX IDX_E2450277FD02F13 ON evenement_intervenant (evenement_id)');
        $this->addSql('CREATE INDEX IDX_E2450277AB9A1716 ON evenement_intervenant (intervenant_id)');
        $this->addSql('CREATE TABLE formation (id INT NOT NULL, composante_id INT NOT NULL, libelle VARCHAR(255) NOT NULL, code_externe VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_404021BFAC12F1AD ON formation (composante_id)');
        $this->addSql('CREATE TABLE inscription (id INT NOT NULL, etudiant_id INT NOT NULL, formation_id INT NOT NULL, debut DATE NOT NULL, fin DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5E90F6D6DDEAB1A3 ON inscription (etudiant_id)');
        $this->addSql('CREATE INDEX IDX_5E90F6D65200282E ON inscription (formation_id)');
        $this->addSql('CREATE TABLE intervenant (id INT NOT NULL, utilisateur_id INT NOT NULL, archive BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_73D0145CFB88E14F ON intervenant (utilisateur_id)');
        $this->addSql('CREATE TABLE intervenant_competence (intervenant_id INT NOT NULL, competence_id INT NOT NULL, PRIMARY KEY(intervenant_id, competence_id))');
        $this->addSql('CREATE INDEX IDX_9DA6213CAB9A1716 ON intervenant_competence (intervenant_id)');
        $this->addSql('CREATE INDEX IDX_9DA6213C15761DAB ON intervenant_competence (competence_id)');
        $this->addSql('CREATE TABLE intervenant_campus (intervenant_id INT NOT NULL, campus_id INT NOT NULL, PRIMARY KEY(intervenant_id, campus_id))');
        $this->addSql('CREATE INDEX IDX_E8773CF7AB9A1716 ON intervenant_campus (intervenant_id)');
        $this->addSql('CREATE INDEX IDX_E8773CF7AF5D55E1 ON intervenant_campus (campus_id)');
        $this->addSql('CREATE TABLE intervenant_type_evenement (intervenant_id INT NOT NULL, type_evenement_id INT NOT NULL, PRIMARY KEY(intervenant_id, type_evenement_id))');
        $this->addSql('CREATE INDEX IDX_2DDFCB42AB9A1716 ON intervenant_type_evenement (intervenant_id)');
        $this->addSql('CREATE INDEX IDX_2DDFCB4288939516 ON intervenant_type_evenement (type_evenement_id)');
        $this->addSql('CREATE TABLE parametre (id INT NOT NULL, cle VARCHAR(255) NOT NULL, valeur VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE periode_rh (id INT NOT NULL, utilisateur_envoi_id INT DEFAULT NULL, debut DATE NOT NULL, fin DATE NOT NULL, butoir DATE NOT NULL, date_envoi DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5304467E21EC157A ON periode_rh (utilisateur_envoi_id)');
        $this->addSql('CREATE TABLE profil_beneficiaire (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, actif BOOLEAN DEFAULT true NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE service (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, actif BOOLEAN DEFAULT true NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE taux_horaire (id INT NOT NULL, type_evenement_id INT NOT NULL, montant NUMERIC(5, 2) NOT NULL, debut DATE NOT NULL, fin DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3C7A19AF88939516 ON taux_horaire (type_evenement_id)');
        $this->addSql('CREATE TABLE type_equipement (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, actif BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE type_evenement (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, actif BOOLEAN DEFAULT true NOT NULL, couleur VARCHAR(255) DEFAULT NULL, visible_par_defaut BOOLEAN DEFAULT true NOT NULL, avec_validation BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE utilisateur (id INT NOT NULL, uid VARCHAR(180) NOT NULL, email VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, admin BOOLEAN DEFAULT false NOT NULL, numero_etudiant INT DEFAULT NULL, email_perso VARCHAR(255) DEFAULT NULL, tel_perso VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3539B0606 ON utilisateur (uid)');
        $this->addSql('CREATE TABLE utilisateur_service (utilisateur_id INT NOT NULL, service_id INT NOT NULL, PRIMARY KEY(utilisateur_id, service_id))');
        $this->addSql('CREATE INDEX IDX_9B966D40FB88E14F ON utilisateur_service (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_9B966D40ED5CA9E6 ON utilisateur_service (service_id)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE beneficiaire ADD CONSTRAINT FK_B140D802275ED078 FOREIGN KEY (profil_id) REFERENCES profil_beneficiaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE beneficiaire ADD CONSTRAINT FK_B140D802FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE beneficiaire ADD CONSTRAINT FK_B140D8026885AC1B FOREIGN KEY (gestionnaire_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EC54C8C93 FOREIGN KEY (type_id) REFERENCES type_evenement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EAF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EAB9A1716 FOREIGN KEY (intervenant_id) REFERENCES intervenant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EAC8EDCFF FOREIGN KEY (periode_prise_en_compte_rh_id) REFERENCES periode_rh (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681EC019AB09 FOREIGN KEY (utilisateur_creation_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E5FB9EE51 FOREIGN KEY (utilisateur_modification_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE evenement_type_equipement ADD CONSTRAINT FK_41113AA6FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE evenement_type_equipement ADD CONSTRAINT FK_41113AA6F082B869 FOREIGN KEY (type_equipement_id) REFERENCES type_equipement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE evenement_beneficiaire ADD CONSTRAINT FK_1D6DB454FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE evenement_beneficiaire ADD CONSTRAINT FK_1D6DB4545AF81F68 FOREIGN KEY (beneficiaire_id) REFERENCES beneficiaire (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE evenement_intervenant ADD CONSTRAINT FK_E2450277FD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE evenement_intervenant ADD CONSTRAINT FK_E2450277AB9A1716 FOREIGN KEY (intervenant_id) REFERENCES intervenant (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BFAC12F1AD FOREIGN KEY (composante_id) REFERENCES composante (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D6DDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE inscription ADD CONSTRAINT FK_5E90F6D65200282E FOREIGN KEY (formation_id) REFERENCES formation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervenant ADD CONSTRAINT FK_73D0145CFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervenant_competence ADD CONSTRAINT FK_9DA6213CAB9A1716 FOREIGN KEY (intervenant_id) REFERENCES intervenant (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervenant_competence ADD CONSTRAINT FK_9DA6213C15761DAB FOREIGN KEY (competence_id) REFERENCES competence (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervenant_campus ADD CONSTRAINT FK_E8773CF7AB9A1716 FOREIGN KEY (intervenant_id) REFERENCES intervenant (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervenant_campus ADD CONSTRAINT FK_E8773CF7AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervenant_type_evenement ADD CONSTRAINT FK_2DDFCB42AB9A1716 FOREIGN KEY (intervenant_id) REFERENCES intervenant (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervenant_type_evenement ADD CONSTRAINT FK_2DDFCB4288939516 FOREIGN KEY (type_evenement_id) REFERENCES type_evenement (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE periode_rh ADD CONSTRAINT FK_5304467E21EC157A FOREIGN KEY (utilisateur_envoi_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE taux_horaire ADD CONSTRAINT FK_3C7A19AF88939516 FOREIGN KEY (type_evenement_id) REFERENCES type_evenement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE utilisateur_service ADD CONSTRAINT FK_9B966D40FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE utilisateur_service ADD CONSTRAINT FK_9B966D40ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE beneficiaire_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE campus_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE competence_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE composante_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE evenement_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE formation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE inscription_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE intervenant_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE parametre_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE periode_rh_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE profil_beneficiaire_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE service_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE taux_horaire_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE type_equipement_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE type_evenement_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE utilisateur_id_seq CASCADE');
        $this->addSql('ALTER TABLE beneficiaire DROP CONSTRAINT FK_B140D802275ED078');
        $this->addSql('ALTER TABLE beneficiaire DROP CONSTRAINT FK_B140D802FB88E14F');
        $this->addSql('ALTER TABLE beneficiaire DROP CONSTRAINT FK_B140D8026885AC1B');
        $this->addSql('ALTER TABLE evenement DROP CONSTRAINT FK_B26681EC54C8C93');
        $this->addSql('ALTER TABLE evenement DROP CONSTRAINT FK_B26681EAF5D55E1');
        $this->addSql('ALTER TABLE evenement DROP CONSTRAINT FK_B26681EAB9A1716');
        $this->addSql('ALTER TABLE evenement DROP CONSTRAINT FK_B26681EAC8EDCFF');
        $this->addSql('ALTER TABLE evenement DROP CONSTRAINT FK_B26681EC019AB09');
        $this->addSql('ALTER TABLE evenement DROP CONSTRAINT FK_B26681E5FB9EE51');
        $this->addSql('ALTER TABLE evenement_type_equipement DROP CONSTRAINT FK_41113AA6FD02F13');
        $this->addSql('ALTER TABLE evenement_type_equipement DROP CONSTRAINT FK_41113AA6F082B869');
        $this->addSql('ALTER TABLE evenement_beneficiaire DROP CONSTRAINT FK_1D6DB454FD02F13');
        $this->addSql('ALTER TABLE evenement_beneficiaire DROP CONSTRAINT FK_1D6DB4545AF81F68');
        $this->addSql('ALTER TABLE evenement_intervenant DROP CONSTRAINT FK_E2450277FD02F13');
        $this->addSql('ALTER TABLE evenement_intervenant DROP CONSTRAINT FK_E2450277AB9A1716');
        $this->addSql('ALTER TABLE formation DROP CONSTRAINT FK_404021BFAC12F1AD');
        $this->addSql('ALTER TABLE inscription DROP CONSTRAINT FK_5E90F6D6DDEAB1A3');
        $this->addSql('ALTER TABLE inscription DROP CONSTRAINT FK_5E90F6D65200282E');
        $this->addSql('ALTER TABLE intervenant DROP CONSTRAINT FK_73D0145CFB88E14F');
        $this->addSql('ALTER TABLE intervenant_competence DROP CONSTRAINT FK_9DA6213CAB9A1716');
        $this->addSql('ALTER TABLE intervenant_competence DROP CONSTRAINT FK_9DA6213C15761DAB');
        $this->addSql('ALTER TABLE intervenant_campus DROP CONSTRAINT FK_E8773CF7AB9A1716');
        $this->addSql('ALTER TABLE intervenant_campus DROP CONSTRAINT FK_E8773CF7AF5D55E1');
        $this->addSql('ALTER TABLE intervenant_type_evenement DROP CONSTRAINT FK_2DDFCB42AB9A1716');
        $this->addSql('ALTER TABLE intervenant_type_evenement DROP CONSTRAINT FK_2DDFCB4288939516');
        $this->addSql('ALTER TABLE periode_rh DROP CONSTRAINT FK_5304467E21EC157A');
        $this->addSql('ALTER TABLE taux_horaire DROP CONSTRAINT FK_3C7A19AF88939516');
        $this->addSql('ALTER TABLE utilisateur_service DROP CONSTRAINT FK_9B966D40FB88E14F');
        $this->addSql('ALTER TABLE utilisateur_service DROP CONSTRAINT FK_9B966D40ED5CA9E6');
        $this->addSql('DROP TABLE beneficiaire');
        $this->addSql('DROP TABLE campus');
        $this->addSql('DROP TABLE competence');
        $this->addSql('DROP TABLE composante');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE evenement_type_equipement');
        $this->addSql('DROP TABLE evenement_beneficiaire');
        $this->addSql('DROP TABLE evenement_intervenant');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE inscription');
        $this->addSql('DROP TABLE intervenant');
        $this->addSql('DROP TABLE intervenant_competence');
        $this->addSql('DROP TABLE intervenant_campus');
        $this->addSql('DROP TABLE intervenant_type_evenement');
        $this->addSql('DROP TABLE parametre');
        $this->addSql('DROP TABLE periode_rh');
        $this->addSql('DROP TABLE profil_beneficiaire');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE taux_horaire');
        $this->addSql('DROP TABLE type_equipement');
        $this->addSql('DROP TABLE type_evenement');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE utilisateur_service');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
