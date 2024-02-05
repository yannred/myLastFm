<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240205164713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE album (id INT AUTO_INCREMENT NOT NULL, mbid VARCHAR(255) NOT NULL, name VARCHAR(1020) NOT NULL, artist_id INT DEFAULT NULL, INDEX IDX_39986E43B7970CF8 (artist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE artist (id INT AUTO_INCREMENT NOT NULL, mbid VARCHAR(255) NOT NULL, name VARCHAR(1020) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, size INT DEFAULT NULL, url VARCHAR(1020) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE scrobble (id INT AUTO_INCREMENT NOT NULL, timestamp INT NOT NULL, track_id INT NOT NULL, INDEX IDX_8EC7C28E5ED23C43 (track_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE track (id INT AUTO_INCREMENT NOT NULL, mbid VARCHAR(255) NOT NULL, name VARCHAR(1020) NOT NULL, url VARCHAR(1020) NOT NULL, artist_id INT DEFAULT NULL, album_id INT DEFAULT NULL, INDEX IDX_D6E3F8A6B7970CF8 (artist_id), INDEX IDX_D6E3F8A61137ABCF (album_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE track_image (track_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_B5CAA6F85ED23C43 (track_id), INDEX IDX_B5CAA6F83DA5256D (image_id), PRIMARY KEY(track_id, image_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E43B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
        $this->addSql('ALTER TABLE scrobble ADD CONSTRAINT FK_8EC7C28E5ED23C43 FOREIGN KEY (track_id) REFERENCES track (id)');
        $this->addSql('ALTER TABLE track ADD CONSTRAINT FK_D6E3F8A6B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id)');
        $this->addSql('ALTER TABLE track ADD CONSTRAINT FK_D6E3F8A61137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
        $this->addSql('ALTER TABLE track_image ADD CONSTRAINT FK_B5CAA6F85ED23C43 FOREIGN KEY (track_id) REFERENCES track (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE track_image ADD CONSTRAINT FK_B5CAA6F83DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E43B7970CF8');
        $this->addSql('ALTER TABLE scrobble DROP FOREIGN KEY FK_8EC7C28E5ED23C43');
        $this->addSql('ALTER TABLE track DROP FOREIGN KEY FK_D6E3F8A6B7970CF8');
        $this->addSql('ALTER TABLE track DROP FOREIGN KEY FK_D6E3F8A61137ABCF');
        $this->addSql('ALTER TABLE track_image DROP FOREIGN KEY FK_B5CAA6F85ED23C43');
        $this->addSql('ALTER TABLE track_image DROP FOREIGN KEY FK_B5CAA6F83DA5256D');
        $this->addSql('DROP TABLE album');
        $this->addSql('DROP TABLE artist');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE scrobble');
        $this->addSql('DROP TABLE track');
        $this->addSql('DROP TABLE track_image');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
