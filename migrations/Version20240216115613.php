<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240216115613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE widget (id INT AUTO_INCREMENT NOT NULL, widget_grid_id INT NOT NULL, code VARCHAR(255) NOT NULL, wording VARCHAR(1020) DEFAULT NULL, INDEX IDX_85F91ED022DE6F54 (widget_grid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE widget_grid (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, code VARCHAR(255) NOT NULL, wording VARCHAR(1020) DEFAULT NULL, default_grid TINYINT(1) NOT NULL, comment LONGTEXT DEFAULT NULL, type_widget INT NOT NULL, sub_type_widget INT DEFAULT NULL, query LONGTEXT DEFAULT NULL, width DOUBLE PRECISION DEFAULT NULL, height DOUBLE PRECISION DEFAULT NULL, position_x DOUBLE PRECISION DEFAULT NULL, position_y DOUBLE PRECISION DEFAULT NULL, font_color VARCHAR(255) DEFAULT NULL, background_color VARCHAR(255) DEFAULT NULL, INDEX IDX_B6D7D288A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE widget ADD CONSTRAINT FK_85F91ED022DE6F54 FOREIGN KEY (widget_grid_id) REFERENCES widget_grid (id)');
        $this->addSql('ALTER TABLE widget_grid ADD CONSTRAINT FK_B6D7D288A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE widget DROP FOREIGN KEY FK_85F91ED022DE6F54');
        $this->addSql('ALTER TABLE widget_grid DROP FOREIGN KEY FK_B6D7D288A76ED395');
        $this->addSql('DROP TABLE widget');
        $this->addSql('DROP TABLE widget_grid');
    }
}
