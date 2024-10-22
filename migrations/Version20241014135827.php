<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241014135827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE song_user (song_id VARCHAR(255) NOT NULL, user_id INT NOT NULL, INDEX IDX_FE0000BCA0BDB2F3 (song_id), INDEX IDX_FE0000BCA76ED395 (user_id), PRIMARY KEY(song_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_song (user_id INT NOT NULL, song_id VARCHAR(255) NOT NULL, INDEX IDX_496CA268A76ED395 (user_id), INDEX IDX_496CA268A0BDB2F3 (song_id), PRIMARY KEY(user_id, song_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE song_user ADD CONSTRAINT FK_FE0000BCA0BDB2F3 FOREIGN KEY (song_id) REFERENCES songs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE song_user ADD CONSTRAINT FK_FE0000BCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_song ADD CONSTRAINT FK_496CA268A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_song ADD CONSTRAINT FK_496CA268A0BDB2F3 FOREIGN KEY (song_id) REFERENCES songs (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE song_user DROP FOREIGN KEY FK_FE0000BCA0BDB2F3');
        $this->addSql('ALTER TABLE song_user DROP FOREIGN KEY FK_FE0000BCA76ED395');
        $this->addSql('ALTER TABLE user_song DROP FOREIGN KEY FK_496CA268A76ED395');
        $this->addSql('ALTER TABLE user_song DROP FOREIGN KEY FK_496CA268A0BDB2F3');
        $this->addSql('DROP TABLE song_user');
        $this->addSql('DROP TABLE user_song');
    }
}
