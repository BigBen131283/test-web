<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221009141112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD is_verified TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE users ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E9B03A8386 ON users (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP is_verified');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9B03A8386');
        $this->addSql('DROP INDEX IDX_1483A5E9B03A8386 ON users');
        $this->addSql('ALTER TABLE users DROP created_by_id');
    }
}
