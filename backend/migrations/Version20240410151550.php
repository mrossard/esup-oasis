<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240410151550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout avis ESE';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE avis_ese_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE avis_ese (id INT NOT NULL, fichier_id INT DEFAULT NULL, utilisateur_id INT NOT NULL, debut DATE NOT NULL, fin DATE DEFAULT NULL, commentaire TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_83EAF70DF915CFE ON avis_ese (fichier_id)');
        $this->addSql('CREATE INDEX IDX_83EAF70DFB88E14F ON avis_ese (utilisateur_id)');
        $this->addSql('ALTER TABLE avis_ese ADD CONSTRAINT FK_83EAF70DF915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE avis_ese ADD CONSTRAINT FK_83EAF70DFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE avis_ese_id_seq CASCADE');
        $this->addSql('ALTER TABLE avis_ese DROP CONSTRAINT FK_83EAF70DF915CFE');
        $this->addSql('ALTER TABLE avis_ese DROP CONSTRAINT FK_83EAF70DFB88E14F');
        $this->addSql('DROP TABLE avis_ese');
    }
}
