<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250627111336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__parents AS SELECT id, full_name, email FROM parents
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE parents
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE parents (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, full_name VARCHAR(100) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, referral_source CLOB DEFAULT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO parents (id, full_name, email) SELECT id, full_name, email FROM __temp__parents
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__parents
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__parents AS SELECT id, full_name, email FROM parents
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE parents
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE parents (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, full_name VARCHAR(100) NOT NULL, email VARCHAR(100) DEFAULT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO parents (id, full_name, email) SELECT id, full_name, email FROM __temp__parents
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__parents
        SQL);
    }
}
