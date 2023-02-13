<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230213172001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande CHANGE telephone telephone VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user ADD roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD nom VARCHAR(255) DEFAULT NULL, ADD prenom VARCHAR(255) DEFAULT NULL, DROP name, DROP last_name, CHANGE email email VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande CHANGE telephone telephone INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD name VARCHAR(255) NOT NULL, ADD last_name VARCHAR(255) NOT NULL, DROP roles, DROP nom, DROP prenom, CHANGE email email VARCHAR(255) NOT NULL');
    }
}
