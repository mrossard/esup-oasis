<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230622105113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE valeur_parametre_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE valeur_parametre (id INT NOT NULL, parametre_id INT NOT NULL, valeur VARCHAR(255) NOT NULL, debut DATE NOT NULL, fin DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_360B3C166358FF62 ON valeur_parametre (parametre_id)');
        $this->addSql('ALTER TABLE valeur_parametre ADD CONSTRAINT FK_360B3C166358FF62 FOREIGN KEY (parametre_id) REFERENCES parametre (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE parametre DROP valeur');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE valeur_parametre_id_seq CASCADE');
        $this->addSql('ALTER TABLE valeur_parametre DROP CONSTRAINT FK_360B3C166358FF62');
        $this->addSql('DROP TABLE valeur_parametre');
        $this->addSql('ALTER TABLE parametre ADD valeur VARCHAR(255) NOT NULL');
    }
}
