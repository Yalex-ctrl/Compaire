<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250627102316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__student AS SELECT id, parent_id, mentor_id, first_name, last_name, full_name, email, address, class_level, subjects, weekly_hours, usual_schedule, status, conv_compt, notes FROM student
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE student
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE student (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, parents_id INTEGER DEFAULT NULL, mentor_id INTEGER DEFAULT NULL, first_name VARCHAR(100) DEFAULT NULL, last_name VARCHAR(100) DEFAULT NULL, full_name VARCHAR(100) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, class_level VARCHAR(255) DEFAULT NULL, subjects CLOB DEFAULT NULL --(DC2Type:json)
            , weekly_hours INTEGER DEFAULT NULL, usual_schedule CLOB DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, conv_compt VARCHAR(255) DEFAULT NULL, notes CLOB DEFAULT NULL, CONSTRAINT FK_B723AF33DB403044 FOREIGN KEY (mentor_id) REFERENCES mentor (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B723AF33B706B6D3 FOREIGN KEY (parents_id) REFERENCES parents (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO student (id, parents_id, mentor_id, first_name, last_name, full_name, email, address, class_level, subjects, weekly_hours, usual_schedule, status, conv_compt, notes) SELECT id, parent_id, mentor_id, first_name, last_name, full_name, email, address, class_level, subjects, weekly_hours, usual_schedule, status, conv_compt, notes FROM __temp__student
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__student
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B723AF33DB403044 ON student (mentor_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B723AF33B706B6D3 ON student (parents_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__student AS SELECT id, parents_id, mentor_id, first_name, last_name, full_name, email, address, class_level, subjects, weekly_hours, usual_schedule, status, conv_compt, notes FROM student
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE student
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE student (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, parent_id INTEGER DEFAULT NULL, mentor_id INTEGER DEFAULT NULL, first_name VARCHAR(100) DEFAULT NULL, last_name VARCHAR(100) DEFAULT NULL, full_name VARCHAR(100) DEFAULT NULL, email VARCHAR(100) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, class_level VARCHAR(255) DEFAULT NULL, subjects CLOB DEFAULT NULL --(DC2Type:json)
            , weekly_hours INTEGER DEFAULT NULL, usual_schedule CLOB DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, conv_compt VARCHAR(255) DEFAULT NULL, notes CLOB DEFAULT NULL, CONSTRAINT FK_B723AF33DB403044 FOREIGN KEY (mentor_id) REFERENCES mentor (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B723AF33727ACA70 FOREIGN KEY (parent_id) REFERENCES parents (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO student (id, parent_id, mentor_id, first_name, last_name, full_name, email, address, class_level, subjects, weekly_hours, usual_schedule, status, conv_compt, notes) SELECT id, parents_id, mentor_id, first_name, last_name, full_name, email, address, class_level, subjects, weekly_hours, usual_schedule, status, conv_compt, notes FROM __temp__student
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__student
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B723AF33DB403044 ON student (mentor_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B723AF33727ACA70 ON student (parent_id)
        SQL);
    }
}
