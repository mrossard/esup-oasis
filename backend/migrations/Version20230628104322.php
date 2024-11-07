<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use DateTime;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230628104322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE intervenant ADD debut DATE NOT NULL default CURRENT_DATE');
        $this->addSql('ALTER TABLE intervenant ADD fin DATE NOT NULL  default CURRENT_DATE');
        $this->addSql('ALTER TABLE intervenant DROP archive');

        //init avec l'année U en cours
        $currentDate = new DateTime();
        $anneeDebut = match (true) {
            $currentDate->format('m') > '08' => (int)$currentDate->format('Y'),
            default => (int)$currentDate->format('Y') - 1
        };
        $this->addSql("update intervenant set debut='" . $anneeDebut . "-09-01', fin='" . $anneeDebut + 1 . "-08-31'");
    }


    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE intervenant ADD archive BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE intervenant DROP debut');
        $this->addSql('ALTER TABLE intervenant DROP fin');
    }
}
