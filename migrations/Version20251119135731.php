<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251119135731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invoice_item (id INT AUTO_INCREMENT NOT NULL, invoice_id INT NOT NULL, description VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL, unit_price DOUBLE PRECISION NOT NULL, tax_rate DOUBLE PRECISION NOT NULL, cgst DOUBLE PRECISION DEFAULT NULL, sgst DOUBLE PRECISION DEFAULT NULL, igst DOUBLE PRECISION DEFAULT NULL, line_total DOUBLE PRECISION NOT NULL, hsn_sac VARCHAR(20) DEFAULT NULL, quantity DOUBLE PRECISION DEFAULT NULL, INDEX IDX_1DDE477B2989F1FD (invoice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoice_item ADD CONSTRAINT FK_1DDE477B2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id)');
        $this->addSql('ALTER TABLE business ADD state VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice DROP items');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice_item DROP FOREIGN KEY FK_1DDE477B2989F1FD');
        $this->addSql('DROP TABLE invoice_item');
        $this->addSql('ALTER TABLE invoice ADD items JSON NOT NULL');
        $this->addSql('ALTER TABLE business DROP state');
    }
}
