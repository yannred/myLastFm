<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208110054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE import (id INT AUTO_INCREMENT NOT NULL, last_scrobble_id INT NOT NULL, user_id INT NOT NULL, date DATE NOT NULL, INDEX IDX_9D4ECE1D31106EE7 (last_scrobble_id), INDEX IDX_9D4ECE1DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE import ADD CONSTRAINT FK_9D4ECE1D31106EE7 FOREIGN KEY (last_scrobble_id) REFERENCES scrobble (id)');
        $this->addSql('ALTER TABLE import ADD CONSTRAINT FK_9D4ECE1DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE scrobble ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE scrobble ADD CONSTRAINT FK_8EC7C28EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8EC7C28EA76ED395 ON scrobble (user_id)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE available_at available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE import DROP FOREIGN KEY FK_9D4ECE1D31106EE7');
        $this->addSql('ALTER TABLE import DROP FOREIGN KEY FK_9D4ECE1DA76ED395');
        $this->addSql('DROP TABLE import');
        $this->addSql('ALTER TABLE scrobble DROP FOREIGN KEY FK_8EC7C28EA76ED395');
        $this->addSql('DROP INDEX IDX_8EC7C28EA76ED395 ON scrobble');
        $this->addSql('ALTER TABLE scrobble DROP user_id');
        $this->addSql('ALTER TABLE messenger_messages CHANGE created_at created_at DATETIME NOT NULL, CHANGE available_at available_at DATETIME NOT NULL, CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }
}
