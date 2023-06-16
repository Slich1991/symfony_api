<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230612131422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__product_image AS SELECT id, product_id, file_path FROM product_image');
        $this->addSql('DROP TABLE product_image');
        $this->addSql('CREATE TABLE product_image (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER DEFAULT NULL, file_path VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_64617F034584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO product_image (id, product_id, file_path) SELECT id, product_id, file_path FROM __temp__product_image');
        $this->addSql('DROP TABLE __temp__product_image');
        $this->addSql('CREATE INDEX IDX_64617F034584665A ON product_image (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__product_image AS SELECT id, product_id, file_path FROM product_image');
        $this->addSql('DROP TABLE product_image');
        $this->addSql('CREATE TABLE product_image (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, product_id INTEGER NOT NULL, file_path VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_64617F034584665A FOREIGN KEY (product_id) REFERENCES "product" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO product_image (id, product_id, file_path) SELECT id, product_id, file_path FROM __temp__product_image');
        $this->addSql('DROP TABLE __temp__product_image');
        $this->addSql('CREATE INDEX IDX_64617F034584665A ON product_image (product_id)');
    }
}
