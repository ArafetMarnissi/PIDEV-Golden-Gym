<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
<<<<<<<< HEAD:Pidev/migrations/Version20230225141640.php
final class Version20230225141640 extends AbstractMigration
========
final class Version20230223095513 extends AbstractMigration
>>>>>>>> 864c783724c82e5fa7599cfcbb8f2672ead0f0ff:Pidev/migrations/Version20230223095513.php
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
<<<<<<<< HEAD:Pidev/migrations/Version20230225141640.php
        $this->addSql('ALTER TABLE user ADD private_key INT NOT NULL, ADD status TINYINT(1) NOT NULL');
========
        $this->addSql('ALTER TABLE user ADD enable_reservation TINYINT(1) NOT NULL');
>>>>>>>> 864c783724c82e5fa7599cfcbb8f2672ead0f0ff:Pidev/migrations/Version20230223095513.php
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
<<<<<<<< HEAD:Pidev/migrations/Version20230225141640.php
        $this->addSql('ALTER TABLE user DROP private_key, DROP status');
========
        $this->addSql('ALTER TABLE user DROP enable_reservation');
>>>>>>>> 864c783724c82e5fa7599cfcbb8f2672ead0f0ff:Pidev/migrations/Version20230223095513.php
    }
}
