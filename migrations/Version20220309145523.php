<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220309145523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, type_user_id INT NOT NULL, avatar_id INT DEFAULT NULL, pseudo VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, cell_phone VARCHAR(255) DEFAULT NULL, salt VARCHAR(255) NOT NULL, status_connected TINYINT(1) NOT NULL, validation_key VARCHAR(255) NOT NULL, activated_user TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D64986CC499D (pseudo), INDEX IDX_8D93D6498F4FBC60 (type_user_id), INDEX IDX_8D93D64986383B10 (avatar_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498F4FBC60 FOREIGN KEY (type_user_id) REFERENCES type_user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64986383B10 FOREIGN KEY (avatar_id) REFERENCES avatar (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE avatar CHANGE legend legend VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE url url VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE type type VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE category CHANGE label label VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE type_media CHANGE group_media group_media VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE type_media type_media VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE type_user CHANGE label label VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
