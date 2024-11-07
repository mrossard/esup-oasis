<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306101549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Gestion de la question "Voulez-vous un accompagnement?" en dur';
    }

    public function up(Schema $schema): void
    {
        /**
         * On ajoute aux étapes un bool "siDemandeAccompagnement"
         * ==> si true + pas de réponse demande accompagnement, on pose la question au lieu des questions de l'étape
         * ==> si true + réponse oui, on pose les questions de l'étape
         * ==> si true + réponse non, on skippe l'étape
         * ==> si false, on pose les questions de l'étape
         *
         * !!!! il faudra en tenir compte dans le calcul du bon remplissage du questionnaire !!!!
         */
        $this->addSql('ALTER TABLE etape_demande ADD si_demande_accompagnement BOOLEAN DEFAULT false NOT NULL');
        
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etape_demande DROP si_demande_accompagnement');
    }
}
