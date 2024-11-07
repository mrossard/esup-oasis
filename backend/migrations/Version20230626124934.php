<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230626124934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE intervention_forfait_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE intervention_forfait (id INT NOT NULL, intervenant_id INT NOT NULL, periode_id INT NOT NULL, type_id INT NOT NULL, heures NUMERIC(5, 1) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F05BB0E7AB9A1716 ON intervention_forfait (intervenant_id)');
        $this->addSql('CREATE INDEX IDX_F05BB0E7F384C1CF ON intervention_forfait (periode_id)');
        $this->addSql('CREATE INDEX IDX_F05BB0E7C54C8C93 ON intervention_forfait (type_id)');
        $this->addSql('ALTER TABLE intervention_forfait ADD CONSTRAINT FK_F05BB0E7AB9A1716 FOREIGN KEY (intervenant_id) REFERENCES intervenant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervention_forfait ADD CONSTRAINT FK_F05BB0E7F384C1CF FOREIGN KEY (periode_id) REFERENCES periode_rh (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE intervention_forfait ADD CONSTRAINT FK_F05BB0E7C54C8C93 FOREIGN KEY (type_id) REFERENCES type_evenement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE type_evenement ADD forfait BOOLEAN DEFAULT false NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE intervention_forfait_id_seq CASCADE');
        $this->addSql('ALTER TABLE intervention_forfait DROP CONSTRAINT FK_F05BB0E7AB9A1716');
        $this->addSql('ALTER TABLE intervention_forfait DROP CONSTRAINT FK_F05BB0E7F384C1CF');
        $this->addSql('ALTER TABLE intervention_forfait DROP CONSTRAINT FK_F05BB0E7C54C8C93');
        $this->addSql('DROP TABLE intervention_forfait');
        $this->addSql('ALTER TABLE type_evenement DROP forfait');
    }
}
