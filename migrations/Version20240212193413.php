<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240212193413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artist_image (artist_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_A531340CB7970CF8 (artist_id), INDEX IDX_A531340C3DA5256D (image_id), PRIMARY KEY(artist_id, image_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE artist_image ADD CONSTRAINT FK_A531340CB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE artist_image ADD CONSTRAINT FK_A531340C3DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE artist ADD url VARCHAR(1020) DEFAULT NULL, ADD listeners BIGINT DEFAULT NULL, ADD playcount BIGINT DEFAULT NULL, ADD bio_summary LONGTEXT DEFAULT NULL, ADD bio_content LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist_image DROP FOREIGN KEY FK_A531340CB7970CF8');
        $this->addSql('ALTER TABLE artist_image DROP FOREIGN KEY FK_A531340C3DA5256D');
        $this->addSql('DROP TABLE artist_image');
        $this->addSql('ALTER TABLE artist DROP url, DROP listeners, DROP playcount, DROP bio_summary, DROP bio_content');
    }
}
