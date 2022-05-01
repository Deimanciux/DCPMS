<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220430210608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE health_record ADD CONSTRAINT FK_E0DE7714A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE health_record ADD CONSTRAINT FK_E0DE7714A2A44441 FOREIGN KEY (tooth_id) REFERENCES tooth (id)');
        $this->addSql('CREATE INDEX IDX_E0DE7714A76ED395 ON health_record (user_id)');
        $this->addSql('CREATE INDEX IDX_E0DE7714A2A44441 ON health_record (tooth_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE health_record DROP FOREIGN KEY FK_E0DE7714A76ED395');
        $this->addSql('ALTER TABLE health_record DROP FOREIGN KEY FK_E0DE7714A2A44441');
        $this->addSql('DROP INDEX IDX_E0DE7714A76ED395 ON health_record');
        $this->addSql('DROP INDEX IDX_E0DE7714A2A44441 ON health_record');
    }
}
