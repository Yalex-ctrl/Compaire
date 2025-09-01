<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250901135929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // 1) Tables "principales" (référencées par d'autres)
        $this->addSql(<<<'SQL'
            CREATE TABLE mentor (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                first_name VARCHAR(100) NOT NULL,
                phone VARCHAR(20) DEFAULT NULL,
                account_report VARCHAR(255) NOT NULL,
                auto_status VARCHAR(255) NOT NULL,
                notes CLOB DEFAULT NULL
            )
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE parents (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                full_name VARCHAR(100) DEFAULT NULL,
                address VARCHAR(255) DEFAULT NULL,
                phone VARCHAR(20) DEFAULT NULL,
                email VARCHAR(100) DEFAULT NULL,
                referral_source CLOB DEFAULT NULL
            )
        SQL);

        // 2) Tables dépendantes de mentor/parents
        $this->addSql(<<<'SQL'
            CREATE TABLE student (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                parents_id INTEGER DEFAULT NULL,
                mentor_id INTEGER DEFAULT NULL,
                first_name VARCHAR(100) DEFAULT NULL,
                last_name VARCHAR(100) DEFAULT NULL,
                full_name VARCHAR(100) DEFAULT NULL,
                email VARCHAR(100) DEFAULT NULL,
                address VARCHAR(255) DEFAULT NULL,
                class_level VARCHAR(255) DEFAULT NULL,
                subjects CLOB DEFAULT NULL --(DC2Type:json)
                ,
                weekly_hours INTEGER DEFAULT NULL,
                usual_schedule CLOB DEFAULT NULL,
                status VARCHAR(255) DEFAULT NULL,
                conv_compt VARCHAR(255) DEFAULT NULL,
                notes CLOB DEFAULT NULL,
                CONSTRAINT FK_B723AF33B706B6D3 FOREIGN KEY (parents_id) REFERENCES parents (id) NOT DEFERRABLE INITIALLY IMMEDIATE,
                CONSTRAINT FK_B723AF33DB403044 FOREIGN KEY (mentor_id) REFERENCES mentor (id) NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B723AF33B706B6D3 ON student (parents_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B723AF33DB403044 ON student (mentor_id)
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE course (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                mentor_id INTEGER DEFAULT NULL,
                subject VARCHAR(255) NOT NULL,
                notes CLOB DEFAULT NULL,
                start_time DATETIME NOT NULL,
                end_time DATETIME NOT NULL,
                status VARCHAR(255) NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                price NUMERIC(10, 2) NOT NULL,
                mentor_notes CLOB DEFAULT NULL,
                payment_status VARCHAR(255) NOT NULL,
                CONSTRAINT FK_169E6FB9DB403044 FOREIGN KEY (mentor_id) REFERENCES mentor (id) NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_169E6FB9DB403044 ON course (mentor_id)
        SQL);

        // 3) Tables de jointure (référencent course/student/mentor)
        $this->addSql(<<<'SQL'
            CREATE TABLE course_student (
                course_id INTEGER NOT NULL,
                student_id INTEGER NOT NULL,
                PRIMARY KEY(course_id, student_id),
                CONSTRAINT FK_BFE0AADF591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
                CONSTRAINT FK_BFE0AADFCB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BFE0AADF591CC992 ON course_student (course_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BFE0AADFCB944F1A ON course_student (student_id)
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE mentor_student (
                mentor_id INTEGER NOT NULL,
                student_id INTEGER NOT NULL,
                PRIMARY KEY(mentor_id, student_id),
                CONSTRAINT FK_2E285416DB403044 FOREIGN KEY (mentor_id) REFERENCES mentor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE,
                CONSTRAINT FK_2E285416CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2E285416DB403044 ON mentor_student (mentor_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2E285416CB944F1A ON mentor_student (student_id)
        SQL);

        // 4) Table indépendante (messagerie)
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                body CLOB NOT NULL,
                headers CLOB NOT NULL,
                queue_name VARCHAR(190) NOT NULL,
                created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
                ,
                available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
                ,
                delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)
        SQL);
    }

}
