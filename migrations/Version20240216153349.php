<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240216153349 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE widget ADD comment LONGTEXT DEFAULT NULL, ADD type_widget INT NOT NULL, ADD sub_type_widget INT DEFAULT NULL, ADD query LONGTEXT DEFAULT NULL, ADD width DOUBLE PRECISION DEFAULT NULL, ADD height DOUBLE PRECISION DEFAULT NULL, ADD position_x DOUBLE PRECISION DEFAULT NULL, ADD position_y DOUBLE PRECISION DEFAULT NULL, ADD font_color VARCHAR(255) DEFAULT NULL, ADD background_color VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE widget_grid DROP comment, DROP type_widget, DROP sub_type_widget, DROP query, DROP width, DROP height, DROP position_x, DROP position_y, DROP font_color, DROP background_color');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE widget_grid ADD comment LONGTEXT DEFAULT NULL, ADD type_widget INT NOT NULL, ADD sub_type_widget INT DEFAULT NULL, ADD query LONGTEXT DEFAULT NULL, ADD width DOUBLE PRECISION DEFAULT NULL, ADD height DOUBLE PRECISION DEFAULT NULL, ADD position_x DOUBLE PRECISION DEFAULT NULL, ADD position_y DOUBLE PRECISION DEFAULT NULL, ADD font_color VARCHAR(255) DEFAULT NULL, ADD background_color VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE widget DROP comment, DROP type_widget, DROP sub_type_widget, DROP query, DROP width, DROP height, DROP position_x, DROP position_y, DROP font_color, DROP background_color');
    }
}
