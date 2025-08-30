<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250829092342 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE poll_option (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, poll_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, votes INTEGER DEFAULT NULL, CONSTRAINT FK_B68343EB3C947C0F FOREIGN KEY (poll_id) REFERENCES poll (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B68343EB3C947C0F ON poll_option (poll_id)');
        $this->addSql('CREATE TABLE poll_vote (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, poll_id INTEGER DEFAULT NULL, option_id INTEGER DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_ED568EBEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_ED568EBE3C947C0F FOREIGN KEY (poll_id) REFERENCES poll (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_ED568EBEA7C41D6F FOREIGN KEY (option_id) REFERENCES poll_option (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_ED568EBEA76ED395 ON poll_vote (user_id)');
        $this->addSql('CREATE INDEX IDX_ED568EBE3C947C0F ON poll_vote (poll_id)');
        $this->addSql('CREATE INDEX IDX_ED568EBEA7C41D6F ON poll_vote (option_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE poll_option');
        $this->addSql('DROP TABLE poll_vote');
    }
}
