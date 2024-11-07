<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320140838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout types de suivi des aménagements';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE type_suivi_amenagement_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE type_suivi_amenagement (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, actif BOOLEAN DEFAULT true NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE type_suivi_amenagement_id_seq CASCADE');
        $this->addSql('DROP TABLE type_suivi_amenagement');
    }
}
