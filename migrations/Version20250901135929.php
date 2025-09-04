<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250901135929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration initiale MySQL: mentor, parents, student, course, relations, messenger_messages';
    }

    public function up(Schema $schema): void
    {
        // Ne jouer que sur MySQL
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQLPlatform,
            'Migration only safe on MySQL.'
        );

        // 1) Tables "principales"
        $this->addSql(<<<'SQL'
            CREATE TABLE mentor (
                id INT AUTO_INCREMENT NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                first_name VARCHAR(100) NOT NULL,
                phone VARCHAR(20) DEFAULT NULL,
                account_report VARCHAR(255) NOT NULL,
                auto_status VARCHAR(255) NOT NULL,
                notes LONGTEXT DEFAULT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE parents (
                id INT AUTO_INCREMENT NOT NULL,
                full_name VARCHAR(100) DEFAULT NULL,
                address VARCHAR(255) DEFAULT NULL,
                phone VARCHAR(20) DEFAULT NULL,
                email VARCHAR(100) DEFAULT NULL,
                referral_source LONGTEXT DEFAULT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        // 2) Tables dépendantes
        $this->addSql(<<<'SQL'
            CREATE TABLE student (
                id INT AUTO_INCREMENT NOT NULL,
                parents_id INT DEFAULT NULL,
                mentor_id INT DEFAULT NULL,
                first_name VARCHAR(100) DEFAULT NULL,
                last_name VARCHAR(100) DEFAULT NULL,
                full_name VARCHAR(100) DEFAULT NULL,
                email VARCHAR(100) DEFAULT NULL,
                address VARCHAR(255) DEFAULT NULL,
                class_level VARCHAR(255) DEFAULT NULL,
                subjects JSON DEFAULT NULL,
                weekly_hours INT DEFAULT NULL,
                usual_schedule LONGTEXT DEFAULT NULL,
                status VARCHAR(255) DEFAULT NULL,
                conv_compt VARCHAR(255) DEFAULT NULL,
                notes LONGTEXT DEFAULT NULL,
                INDEX IDX_STUDENT_PARENTS (parents_id),
                INDEX IDX_STUDENT_MENTOR (mentor_id),
                PRIMARY KEY(id),
                CONSTRAINT FK_STUDENT_PARENTS FOREIGN KEY (parents_id) REFERENCES parents (id),
                CONSTRAINT FK_STUDENT_MENTOR FOREIGN KEY (mentor_id) REFERENCES mentor (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE course (
                id INT AUTO_INCREMENT NOT NULL,
                mentor_id INT DEFAULT NULL,
                subject VARCHAR(255) NOT NULL,
                notes LONGTEXT DEFAULT NULL,
                start_time DATETIME NOT NULL,
                end_time DATETIME NOT NULL,
                status VARCHAR(255) NOT NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                price DECIMAL(10, 2) NOT NULL,
                mentor_notes LONGTEXT DEFAULT NULL,
                payment_status VARCHAR(255) NOT NULL,
                INDEX IDX_COURSE_MENTOR (mentor_id),
                PRIMARY KEY(id),
                CONSTRAINT FK_COURSE_MENTOR FOREIGN KEY (mentor_id) REFERENCES mentor (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        // 3) Tables de jointure
        $this->addSql(<<<'SQL'
            CREATE TABLE course_student (
                course_id INT NOT NULL,
                student_id INT NOT NULL,
                PRIMARY KEY(course_id, student_id),
                INDEX IDX_CS_COURSE (course_id),
                INDEX IDX_CS_STUDENT (student_id),
                CONSTRAINT FK_CS_COURSE FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE,
                CONSTRAINT FK_CS_STUDENT FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        $this->addSql(<<<'SQL'
            CREATE TABLE mentor_student (
                mentor_id INT NOT NULL,
                student_id INT NOT NULL,
                PRIMARY KEY(mentor_id, student_id),
                INDEX IDX_MS_MENTOR (mentor_id),
                INDEX IDX_MS_STUDENT (student_id),
                CONSTRAINT FK_MS_MENTOR FOREIGN KEY (mentor_id) REFERENCES mentor (id) ON DELETE CASCADE,
                CONSTRAINT FK_MS_STUDENT FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);

        // 4) Messenger
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (
                id BIGINT AUTO_INCREMENT NOT NULL,
                body LONGTEXT NOT NULL,
                headers LONGTEXT NOT NULL,
                queue_name VARCHAR(190) NOT NULL,
                created_at DATETIME NOT NULL,
                available_at DATETIME NOT NULL,
                delivered_at DATETIME DEFAULT NULL,
                INDEX IDX_MESSENGER_QUEUE (queue_name),
                INDEX IDX_MESSENGER_AVAILABLE_AT (available_at),
                INDEX IDX_MESSENGER_DELIVERED_AT (delivered_at),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof MySQLPlatform,
            'Migration only safe on MySQL.'
        );

        // Drop dans l'ordre inverse des dépendances
        $this->addSql('DROP TABLE IF EXISTS course_student');
        $this->addSql('DROP TABLE IF EXISTS mentor_student');
        $this->addSql('DROP TABLE IF EXISTS course');
        $this->addSql('DROP TABLE IF EXISTS student');
        $this->addSql('DROP TABLE IF EXISTS mentor');
        $this->addSql('DROP TABLE IF EXISTS parents');
        $this->addSql('DROP TABLE IF EXISTS messenger_messages');
    }
}
