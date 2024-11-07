<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240111155714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE campagne_demande_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE commission_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE demande_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE etape_demande_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE etat_demande_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE option_reponse_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE question_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE question_etape_demande_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reponse_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE type_demande_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE campagne_demande (id INT NOT NULL, type_demande_id INT NOT NULL, commission_id INT DEFAULT NULL, debut DATE NOT NULL, fin DATE NOT NULL, libelle VARCHAR(255) DEFAULT NULL, date_commission DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A218D5989DEA883D ON campagne_demande (type_demande_id)');
        $this->addSql('CREATE INDEX IDX_A218D598202D1EB2 ON campagne_demande (commission_id)');
        $this->addSql('CREATE TABLE commission (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE commission_utilisateur (commission_id INT NOT NULL, utilisateur_id INT NOT NULL, PRIMARY KEY(commission_id, utilisateur_id))');
        $this->addSql('CREATE INDEX IDX_C5625C2E202D1EB2 ON commission_utilisateur (commission_id)');
        $this->addSql('CREATE INDEX IDX_C5625C2EFB88E14F ON commission_utilisateur (utilisateur_id)');
        $this->addSql('CREATE TABLE demande (id INT NOT NULL, campagne_id INT NOT NULL, demandeur_id INT NOT NULL, etat_id INT NOT NULL, date_depot TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2694D7A516227374 ON demande (campagne_id)');
        $this->addSql('CREATE INDEX IDX_2694D7A595A6EE59 ON demande (demandeur_id)');
        $this->addSql('CREATE INDEX IDX_2694D7A5D5E86FF ON demande (etat_id)');
        $this->addSql('CREATE TABLE etape_demande (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, ordre INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE etape_demande_type_demande (etape_demande_id INT NOT NULL, type_demande_id INT NOT NULL, PRIMARY KEY(etape_demande_id, type_demande_id))');
        $this->addSql('CREATE INDEX IDX_72503405B071B244 ON etape_demande_type_demande (etape_demande_id)');
        $this->addSql('CREATE INDEX IDX_725034059DEA883D ON etape_demande_type_demande (type_demande_id)');
        $this->addSql('CREATE TABLE etat_demande (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, actif BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE option_reponse (id INT NOT NULL, question_id INT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4742C9B21E27F6BF ON option_reponse (question_id)');
        $this->addSql('CREATE TABLE option_reponse_question (option_reponse_id INT NOT NULL, question_id INT NOT NULL, PRIMARY KEY(option_reponse_id, question_id))');
        $this->addSql('CREATE INDEX IDX_5A85E6AD2271A3B2 ON option_reponse_question (option_reponse_id)');
        $this->addSql('CREATE INDEX IDX_5A85E6AD1E27F6BF ON option_reponse_question (question_id)');
        $this->addSql('CREATE TABLE question (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, aide TEXT DEFAULT NULL, type_reponse VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE question_etape_demande (id INT NOT NULL, etape_id INT NOT NULL, question_id INT NOT NULL, ordre INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E5BCBE394A8CA2AD ON question_etape_demande (etape_id)');
        $this->addSql('CREATE INDEX IDX_E5BCBE391E27F6BF ON question_etape_demande (question_id)');
        $this->addSql('CREATE TABLE reponse (id INT NOT NULL, question_id INT NOT NULL, repondant_id INT NOT NULL, option_choisie_id INT DEFAULT NULL, campagne_id INT NOT NULL, commentaire TEXT DEFAULT NULL, date_modification TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5FB6DEC71E27F6BF ON reponse (question_id)');
        $this->addSql('CREATE INDEX IDX_5FB6DEC7C5DBCCD6 ON reponse (repondant_id)');
        $this->addSql('CREATE INDEX IDX_5FB6DEC7480126E8 ON reponse (option_choisie_id)');
        $this->addSql('CREATE INDEX IDX_5FB6DEC716227374 ON reponse (campagne_id)');
        $this->addSql('CREATE TABLE type_demande (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE type_demande_profil_beneficiaire (type_demande_id INT NOT NULL, profil_beneficiaire_id INT NOT NULL, PRIMARY KEY(type_demande_id, profil_beneficiaire_id))');
        $this->addSql('CREATE INDEX IDX_B0A2965B9DEA883D ON type_demande_profil_beneficiaire (type_demande_id)');
        $this->addSql('CREATE INDEX IDX_B0A2965B18607C3D ON type_demande_profil_beneficiaire (profil_beneficiaire_id)');
        $this->addSql('ALTER TABLE campagne_demande ADD CONSTRAINT FK_A218D5989DEA883D FOREIGN KEY (type_demande_id) REFERENCES type_demande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE campagne_demande ADD CONSTRAINT FK_A218D598202D1EB2 FOREIGN KEY (commission_id) REFERENCES commission (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commission_utilisateur ADD CONSTRAINT FK_C5625C2E202D1EB2 FOREIGN KEY (commission_id) REFERENCES commission (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE commission_utilisateur ADD CONSTRAINT FK_C5625C2EFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A516227374 FOREIGN KEY (campagne_id) REFERENCES campagne_demande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A595A6EE59 FOREIGN KEY (demandeur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A5D5E86FF FOREIGN KEY (etat_id) REFERENCES etat_demande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE etape_demande_type_demande ADD CONSTRAINT FK_72503405B071B244 FOREIGN KEY (etape_demande_id) REFERENCES etape_demande (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE etape_demande_type_demande ADD CONSTRAINT FK_725034059DEA883D FOREIGN KEY (type_demande_id) REFERENCES type_demande (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE option_reponse ADD CONSTRAINT FK_4742C9B21E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE option_reponse_question ADD CONSTRAINT FK_5A85E6AD2271A3B2 FOREIGN KEY (option_reponse_id) REFERENCES option_reponse (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE option_reponse_question ADD CONSTRAINT FK_5A85E6AD1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE question_etape_demande ADD CONSTRAINT FK_E5BCBE394A8CA2AD FOREIGN KEY (etape_id) REFERENCES etape_demande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE question_etape_demande ADD CONSTRAINT FK_E5BCBE391E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC71E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7C5DBCCD6 FOREIGN KEY (repondant_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC7480126E8 FOREIGN KEY (option_choisie_id) REFERENCES option_reponse (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC716227374 FOREIGN KEY (campagne_id) REFERENCES campagne_demande (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE type_demande_profil_beneficiaire ADD CONSTRAINT FK_B0A2965B9DEA883D FOREIGN KEY (type_demande_id) REFERENCES type_demande (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE type_demande_profil_beneficiaire ADD CONSTRAINT FK_B0A2965B18607C3D FOREIGN KEY (profil_beneficiaire_id) REFERENCES profil_beneficiaire (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE campagne_demande_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE commission_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE demande_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE etape_demande_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE etat_demande_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE option_reponse_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE question_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE question_etape_demande_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reponse_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE type_demande_id_seq CASCADE');
        $this->addSql('ALTER TABLE campagne_demande DROP CONSTRAINT FK_A218D5989DEA883D');
        $this->addSql('ALTER TABLE campagne_demande DROP CONSTRAINT FK_A218D598202D1EB2');
        $this->addSql('ALTER TABLE commission_utilisateur DROP CONSTRAINT FK_C5625C2E202D1EB2');
        $this->addSql('ALTER TABLE commission_utilisateur DROP CONSTRAINT FK_C5625C2EFB88E14F');
        $this->addSql('ALTER TABLE demande DROP CONSTRAINT FK_2694D7A516227374');
        $this->addSql('ALTER TABLE demande DROP CONSTRAINT FK_2694D7A595A6EE59');
        $this->addSql('ALTER TABLE demande DROP CONSTRAINT FK_2694D7A5D5E86FF');
        $this->addSql('ALTER TABLE etape_demande_type_demande DROP CONSTRAINT FK_72503405B071B244');
        $this->addSql('ALTER TABLE etape_demande_type_demande DROP CONSTRAINT FK_725034059DEA883D');
        $this->addSql('ALTER TABLE option_reponse DROP CONSTRAINT FK_4742C9B21E27F6BF');
        $this->addSql('ALTER TABLE option_reponse_question DROP CONSTRAINT FK_5A85E6AD2271A3B2');
        $this->addSql('ALTER TABLE option_reponse_question DROP CONSTRAINT FK_5A85E6AD1E27F6BF');
        $this->addSql('ALTER TABLE question_etape_demande DROP CONSTRAINT FK_E5BCBE394A8CA2AD');
        $this->addSql('ALTER TABLE question_etape_demande DROP CONSTRAINT FK_E5BCBE391E27F6BF');
        $this->addSql('ALTER TABLE reponse DROP CONSTRAINT FK_5FB6DEC71E27F6BF');
        $this->addSql('ALTER TABLE reponse DROP CONSTRAINT FK_5FB6DEC7C5DBCCD6');
        $this->addSql('ALTER TABLE reponse DROP CONSTRAINT FK_5FB6DEC7480126E8');
        $this->addSql('ALTER TABLE reponse DROP CONSTRAINT FK_5FB6DEC716227374');
        $this->addSql('ALTER TABLE type_demande_profil_beneficiaire DROP CONSTRAINT FK_B0A2965B9DEA883D');
        $this->addSql('ALTER TABLE type_demande_profil_beneficiaire DROP CONSTRAINT FK_B0A2965B18607C3D');
        $this->addSql('DROP TABLE campagne_demande');
        $this->addSql('DROP TABLE commission');
        $this->addSql('DROP TABLE commission_utilisateur');
        $this->addSql('DROP TABLE demande');
        $this->addSql('DROP TABLE etape_demande');
        $this->addSql('DROP TABLE etape_demande_type_demande');
        $this->addSql('DROP TABLE etat_demande');
        $this->addSql('DROP TABLE option_reponse');
        $this->addSql('DROP TABLE option_reponse_question');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE question_etape_demande');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE type_demande');
        $this->addSql('DROP TABLE type_demande_profil_beneficiaire');
    }
}
