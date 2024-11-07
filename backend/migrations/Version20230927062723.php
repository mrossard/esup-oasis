<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Parametre;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20230927062723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Renommage rappel renforts => rappel envoi RH';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("update parametre 
                            set cle = '" . Parametre::FREQUENCE_RAPPEL_ENVOI_RH . "'
                            where cle = 'FREQUENCE_RAPPEL_RENFORTS'");

    }

    public function down(Schema $schema): void
    {
        $this->addSql("update parametre 
                            set cle = 'FREQUENCE_RAPPEL_RENFORTS'
                            where cle = '" . Parametre::FREQUENCE_RAPPEL_ENVOI_RH . "'");
    }
}
