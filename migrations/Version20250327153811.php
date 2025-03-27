<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250327153811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE employees (id VARCHAR(255) NOT NULL, user_name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE work_times (id INT AUTO_INCREMENT NOT NULL, employee_id VARCHAR(255) DEFAULT NULL, date_time_start DATETIME NOT NULL, date_time_end DATETIME NOT NULL, date DATE NOT NULL, INDEX IDX_352C95A38C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE work_times ADD CONSTRAINT FK_352C95A38C03F15C FOREIGN KEY (employee_id) REFERENCES employees (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE work_times DROP FOREIGN KEY FK_352C95A38C03F15C
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE employees
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE work_times
        SQL);
    }
}
