<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250626165951 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__mentor AS SELECT id, last_name, first_name, phone, account_report, auto_status, notes FROM mentor
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE mentor
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE mentor (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, last_name VARCHAR(100) NOT NULL, first_name VARCHAR(100) NOT NULL, phone VARCHAR(20) DEFAULT NULL, account_report VARCHAR(255) NOT NULL, auto_status VARCHAR(255) NOT NULL, notes CLOB DEFAULT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO mentor (id, last_name, first_name, phone, account_report, auto_status, notes) SELECT id, last_name, first_name, phone, account_report, auto_status, notes FROM __temp__mentor
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__mentor
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE mentor ADD COLUMN full_name VARCHAR(100) NOT NULL
        SQL);
    }
}
