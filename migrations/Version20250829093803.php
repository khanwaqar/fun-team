<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250829093803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__poll AS SELECT id, event_id, title, description, status, created_at FROM poll');
        $this->addSql('DROP TABLE poll');
        $this->addSql('CREATE TABLE poll (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, event_id INTEGER DEFAULT NULL, winner_option_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , end_date DATETIME DEFAULT NULL, CONSTRAINT FK_84BCFA4571F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_84BCFA4556567ADC FOREIGN KEY (winner_option_id) REFERENCES poll_option (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO poll (id, event_id, title, description, status, created_at) SELECT id, event_id, title, description, status, created_at FROM __temp__poll');
        $this->addSql('DROP TABLE __temp__poll');
        $this->addSql('CREATE INDEX IDX_84BCFA4571F7E88B ON poll (event_id)');
        $this->addSql('CREATE INDEX IDX_84BCFA4556567ADC ON poll (winner_option_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__poll AS SELECT id, event_id, title, description, status, created_at FROM poll');
        $this->addSql('DROP TABLE poll');
        $this->addSql('CREATE TABLE poll (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, event_id INTEGER DEFAULT NULL, title VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_84BCFA4571F7E88B FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO poll (id, event_id, title, description, status, created_at) SELECT id, event_id, title, description, status, created_at FROM __temp__poll');
        $this->addSql('DROP TABLE __temp__poll');
        $this->addSql('CREATE INDEX IDX_84BCFA4571F7E88B ON poll (event_id)');
    }
}
