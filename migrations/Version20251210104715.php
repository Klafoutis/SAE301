<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251210104715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'promotion et unavailable sur user';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE promotion (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(50) DEFAULT NULL, percentage INT NOT NULL, ative TINYINT NOT NULL, valid_until DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE unavailability (id INT AUTO_INCREMENT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, reason VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE promotion');
        $this->addSql('DROP TABLE unavailability');
    }
}
