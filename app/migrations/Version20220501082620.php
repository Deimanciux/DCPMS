<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220501082620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE health_record DROP FOREIGN KEY FK_E0DE7714A2A44441');
        $this->addSql('DROP INDEX IDX_E0DE7714A2A44441 ON health_record');
        $this->addSql('ALTER TABLE health_record CHANGE tooth_id position_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE health_record ADD CONSTRAINT FK_E0DE7714DD842E46 FOREIGN KEY (position_id) REFERENCES position (id)');
        $this->addSql('CREATE INDEX IDX_E0DE7714DD842E46 ON health_record (position_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE health_record DROP FOREIGN KEY FK_E0DE7714DD842E46');
        $this->addSql('DROP INDEX IDX_E0DE7714DD842E46 ON health_record');
        $this->addSql('ALTER TABLE health_record CHANGE position_id tooth_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE health_record ADD CONSTRAINT FK_E0DE7714A2A44441 FOREIGN KEY (tooth_id) REFERENCES tooth (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_E0DE7714A2A44441 ON health_record (tooth_id)');
    }
}
