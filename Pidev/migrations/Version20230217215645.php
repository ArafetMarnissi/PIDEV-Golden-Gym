<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230217215645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE coach (id INT AUTO_INCREMENT NOT NULL, nom_coach VARCHAR(255) NOT NULL, age_coach INT NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activite ADD coach_id INT DEFAULT NULL, ADD time_activite TIME DEFAULT NULL, DROP coach');
        $this->addSql('ALTER TABLE activite ADD CONSTRAINT FK_B87555153C105691 FOREIGN KEY (coach_id) REFERENCES coach (id)');
        $this->addSql('CREATE INDEX IDX_B87555153C105691 ON activite (coach_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activite DROP FOREIGN KEY FK_B87555153C105691');
        $this->addSql('DROP TABLE coach');
        $this->addSql('DROP INDEX IDX_B87555153C105691 ON activite');
        $this->addSql('ALTER TABLE activite ADD coach VARCHAR(255) NOT NULL, DROP coach_id, DROP time_activite');
    }
}
