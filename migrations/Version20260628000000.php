<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260628000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create JOURNAL_SETTING table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS JOURNAL_SETTING (
            ID INT NOT NULL AUTO_INCREMENT,
            RVID INT NOT NULL,
            SETTING JSON NOT NULL,
            CREATED_AT DATETIME DEFAULT NULL,
            UPDATED_AT DATETIME NOT NULL,
            PRIMARY KEY (ID),
            UNIQUE KEY UNIQ_JOURNAL_SETTING_RVID (RVID)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS JOURNAL_SETTING');
    }
}