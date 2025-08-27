<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250627104759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE course_student (course_id INTEGER NOT NULL, student_id INTEGER NOT NULL, PRIMARY KEY(course_id, student_id), CONSTRAINT FK_BFE0AADF591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BFE0AADFCB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BFE0AADF591CC992 ON course_student (course_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BFE0AADFCB944F1A ON course_student (student_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__course AS SELECT id, mentor_id, start_time, end_time, notes FROM course
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE course
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE course (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, mentor_id INTEGER DEFAULT NULL, start_time DATETIME NOT NULL, end_time DATETIME NOT NULL, notes CLOB DEFAULT NULL, subject VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, price NUMERIC(10, 2) NOT NULL, mentor_notes CLOB DEFAULT NULL, payment_status VARCHAR(255) NOT NULL, CONSTRAINT FK_169E6FB9DB403044 FOREIGN KEY (mentor_id) REFERENCES mentor (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO course (id, mentor_id, start_time, end_time, notes) SELECT id, mentor_id, start_time, end_time, notes FROM __temp__course
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__course
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_169E6FB9DB403044 ON course (mentor_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE course_student
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__course AS SELECT id, mentor_id, notes, start_time, end_time FROM course
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE course
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE course (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, mentor_id INTEGER DEFAULT NULL, student_id INTEGER DEFAULT NULL, notes CLOB DEFAULT NULL, start_time DATETIME NOT NULL, end_time DATETIME NOT NULL, topic VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_169E6FB9DB403044 FOREIGN KEY (mentor_id) REFERENCES mentor (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_169E6FB9CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO course (id, mentor_id, notes, start_time, end_time) SELECT id, mentor_id, notes, start_time, end_time FROM __temp__course
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__course
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_169E6FB9DB403044 ON course (mentor_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_169E6FB9CB944F1A ON course (student_id)
        SQL);
    }
}
