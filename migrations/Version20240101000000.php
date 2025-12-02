<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240101000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema for Sprachlernplattform';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, name VARCHAR(120) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE TABLE course (id UUID NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, level VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE lesson (id UUID NOT NULL, course_id UUID NOT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F87474F9591CC992 ON lesson (course_id)');
        $this->addSql('CREATE TABLE quiz (id UUID NOT NULL, course_id UUID NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A412FA92F59CC992 ON quiz (course_id)');
        $this->addSql('CREATE TABLE quiz_question (id UUID NOT NULL, quiz_id UUID NOT NULL, question TEXT NOT NULL, answers JSON NOT NULL, correct_answer VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EA3E3C298B1A972 ON quiz_question (quiz_id)');
        $this->addSql('CREATE TABLE vocabulary (id UUID NOT NULL, course_id UUID NOT NULL, word VARCHAR(255) NOT NULL, translation VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1B6DD46F59CC992 ON vocabulary (course_id)');
        $this->addSql('CREATE TABLE progress (id UUID NOT NULL, user_id UUID NOT NULL, course_id UUID NOT NULL, progress_value INT NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_93A5C989A76ED395 ON progress (user_id)');
        $this->addSql('CREATE INDEX IDX_93A5C989F59CC992 ON progress (course_id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F9591CC992 FOREIGN KEY (course_id) REFERENCES course (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92F59CC992 FOREIGN KEY (course_id) REFERENCES course (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_question ADD CONSTRAINT FK_EA3E3C298B1A972 FOREIGN KEY (quiz_id) REFERENCES quiz (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vocabulary ADD CONSTRAINT FK_1B6DD46F59CC992 FOREIGN KEY (course_id) REFERENCES course (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE progress ADD CONSTRAINT FK_93A5C989A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE progress ADD CONSTRAINT FK_93A5C989F59CC992 FOREIGN KEY (course_id) REFERENCES course (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE lesson DROP CONSTRAINT FK_F87474F9591CC992');
        $this->addSql('ALTER TABLE quiz DROP CONSTRAINT FK_A412FA92F59CC992');
        $this->addSql('ALTER TABLE quiz_question DROP CONSTRAINT FK_EA3E3C298B1A972');
        $this->addSql('ALTER TABLE vocabulary DROP CONSTRAINT FK_1B6DD46F59CC992');
        $this->addSql('ALTER TABLE progress DROP CONSTRAINT FK_93A5C989A76ED395');
        $this->addSql('ALTER TABLE progress DROP CONSTRAINT FK_93A5C989F59CC992');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE quiz_question');
        $this->addSql('DROP TABLE vocabulary');
        $this->addSql('DROP TABLE progress');
    }
}
