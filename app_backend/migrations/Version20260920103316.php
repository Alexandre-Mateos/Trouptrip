<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260920103316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip DROP CONSTRAINT fk_7656f53b7e3c61f9');
        $this->addSql('DROP INDEX idx_7656f53b7e3c61f9');
        $this->addSql('ALTER TABLE trip DROP owner_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT fk_7656f53b7e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_7656f53b7e3c61f9 ON trip (owner_id)');
    }
}
