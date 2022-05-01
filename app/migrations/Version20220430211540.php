<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220430211540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE health_record ADD diagnosis_id INT DEFAULT NULL, DROP title, DROP description');
        $this->addSql('ALTER TABLE health_record ADD CONSTRAINT FK_E0DE77143CBE4D00 FOREIGN KEY (diagnosis_id) REFERENCES diagnosis (id)');
        $this->addSql('CREATE INDEX IDX_E0DE77143CBE4D00 ON health_record (diagnosis_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE health_record DROP FOREIGN KEY FK_E0DE77143CBE4D00');
        $this->addSql('DROP INDEX IDX_E0DE77143CBE4D00 ON health_record');
        $this->addSql('ALTER TABLE health_record ADD title VARCHAR(50) NOT NULL, ADD description VARCHAR(500) DEFAULT NULL, DROP diagnosis_id');
    }
}
