<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241014152107 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artist_user (artist_id VARCHAR(255) NOT NULL, user_id INT NOT NULL, INDEX IDX_312D50D6B7970CF8 (artist_id), INDEX IDX_312D50D6A76ED395 (user_id), PRIMARY KEY(artist_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_artist (user_id INT NOT NULL, artist_id VARCHAR(255) NOT NULL, INDEX IDX_640B8DBAA76ED395 (user_id), INDEX IDX_640B8DBAB7970CF8 (artist_id), PRIMARY KEY(user_id, artist_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE artist_user ADD CONSTRAINT FK_312D50D6B7970CF8 FOREIGN KEY (artist_id) REFERENCES artists (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE artist_user ADD CONSTRAINT FK_312D50D6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_artist ADD CONSTRAINT FK_640B8DBAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_artist ADD CONSTRAINT FK_640B8DBAB7970CF8 FOREIGN KEY (artist_id) REFERENCES artists (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist_user DROP FOREIGN KEY FK_312D50D6B7970CF8');
        $this->addSql('ALTER TABLE artist_user DROP FOREIGN KEY FK_312D50D6A76ED395');
        $this->addSql('ALTER TABLE user_artist DROP FOREIGN KEY FK_640B8DBAA76ED395');
        $this->addSql('ALTER TABLE user_artist DROP FOREIGN KEY FK_640B8DBAB7970CF8');
        $this->addSql('DROP TABLE artist_user');
        $this->addSql('DROP TABLE user_artist');
    }
}
