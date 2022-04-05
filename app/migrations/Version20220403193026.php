<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220403193026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE personal_code personal_code VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE name name VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE surname surname VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE phone phone VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(4096) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE roles roles TINYTEXT NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:simple_array)\'');
    }
}
