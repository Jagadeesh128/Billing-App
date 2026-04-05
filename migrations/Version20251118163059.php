<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251118163059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE billing_settings (id INT AUTO_INCREMENT NOT NULL, business_id INT NOT NULL, invoice_prefix VARCHAR(255) NOT NULL, invoice_start_number INT NOT NULL, default_tax_rate DOUBLE PRECISION NOT NULL, terms LONGTEXT DEFAULT NULL, logo_url VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_FD99C939A89DB457 (business_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE business (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, gst_enabled TINYINT(1) NOT NULL, gstin VARCHAR(255) DEFAULT NULL, address LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_8D36E387E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, business_id INT NOT NULL, invoice_number VARCHAR(255) NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', customer_name VARCHAR(255) NOT NULL, customer_phone VARCHAR(255) DEFAULT NULL, customer_address VARCHAR(255) DEFAULT NULL, items JSON NOT NULL, sub_total DOUBLE PRECISION NOT NULL, tax_total DOUBLE PRECISION NOT NULL, total DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_90651744A89DB457 (business_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE billing_settings ADD CONSTRAINT FK_FD99C939A89DB457 FOREIGN KEY (business_id) REFERENCES business (id)');
        $this->addSql('ALTER TABLE business ADD CONSTRAINT FK_8D36E387E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744A89DB457 FOREIGN KEY (business_id) REFERENCES business (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE billing_settings DROP FOREIGN KEY FK_FD99C939A89DB457');
        $this->addSql('ALTER TABLE business DROP FOREIGN KEY FK_8D36E387E3C61F9');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744A89DB457');
        $this->addSql('DROP TABLE billing_settings');
        $this->addSql('DROP TABLE business');
        $this->addSql('DROP TABLE invoice');
    }
}
