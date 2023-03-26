<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230306182151 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activite ADD background_color VARCHAR(7) DEFAULT NULL, ADD border_color VARCHAR(7) DEFAULT NULL, ADD text_color VARCHAR(7) DEFAULT NULL, ADD end TIME DEFAULT NULL, CHANGE duree_activite duree_activite TIME DEFAULT NULL');
        $this->addSql('ALTER TABLE participation ADD user_id INT DEFAULT NULL, CHANGE activite_id activite_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AB55E24FA76ED395 ON participation (user_id)');
        $this->addSql('ALTER TABLE produit ADD note DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD private_key INT NOT NULL, CHANGE enable_reservation status TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activite DROP background_color, DROP border_color, DROP text_color, DROP end, CHANGE duree_activite duree_activite VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FA76ED395');
        $this->addSql('DROP INDEX IDX_AB55E24FA76ED395 ON participation');
        $this->addSql('ALTER TABLE participation DROP user_id, CHANGE activite_id activite_id INT NOT NULL');
        $this->addSql('ALTER TABLE produit DROP note');
        $this->addSql('ALTER TABLE user DROP private_key, CHANGE status enable_reservation TINYINT(1) NOT NULL');
    }
}
