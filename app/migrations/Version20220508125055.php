<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220508125055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_schedule ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE work_schedule ADD CONSTRAINT FK_8F8D9BA7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8F8D9BA7A76ED395 ON work_schedule (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_schedule DROP FOREIGN KEY FK_8F8D9BA7A76ED395');
        $this->addSql('DROP INDEX IDX_8F8D9BA7A76ED395 ON work_schedule');
        $this->addSql('ALTER TABLE work_schedule DROP user_id');
    }
}
