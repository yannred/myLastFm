<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240229182100 extends AbstractMigration
{
  public function getDescription(): string
  {
    return '';
  }

  public function up(Schema $schema): void
  {
    // this up() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE import ADD started TINYINT(1) DEFAULT 0 NOT NULL, ADD finalized TINYINT(1) DEFAULT 0 NOT NULL, ADD error TINYINT(1) DEFAULT 0 NOT NULL, ADD error_message LONGTEXT DEFAULT NULL, ADD total_scrobble BIGINT DEFAULT NULL, ADD finalized_scrobble BIGINT DEFAULT NULL, DROP successful');
  }

  public function down(Schema $schema): void
  {
    // this down() migration is auto-generated, please modify it to your needs
    $this->addSql('ALTER TABLE import ADD successful TINYINT(1) NOT NULL, DROP started, DROP finalized, DROP error, DROP error_message, DROP total_scrobble, DROP finalized_scrobble');
  }
}
