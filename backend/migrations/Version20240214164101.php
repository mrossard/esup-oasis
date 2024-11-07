<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240214164101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout table type_engagement';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE type_engagement_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE type_engagement (id INT NOT NULL, libelle VARCHAR(255) NOT NULL, actif BOOLEAN DEFAULT true NOT NULL, PRIMARY KEY(id))');
        $this->addSql("insert into type_engagement(id, libelle, actif)
                            values(nextval('type_engagement_id_seq'), 'Responsabilité associative', true)");
        $this->addSql("insert into type_engagement(id, libelle, actif)
                            values(nextval('type_engagement_id_seq'), 'Réserve militaire', true)");
        $this->addSql("insert into type_engagement(id, libelle, actif)
                            values(nextval('type_engagement_id_seq'), 'Service civique', true)");
        $this->addSql("insert into type_engagement(id, libelle, actif)
                            values(nextval('type_engagement_id_seq'), 'Volontariat militaire', true)");
        $this->addSql("insert into type_engagement(id, libelle, actif)
                            values(nextval('type_engagement_id_seq'), 'Elu(e) UB', true)");
        $this->addSql("insert into type_engagement(id, libelle, actif)
                            values(nextval('type_engagement_id_seq'), 'Elu(e) CROUS', true)");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE type_engagement_id_seq CASCADE');
        $this->addSql('DROP TABLE type_engagement');
    }
}
