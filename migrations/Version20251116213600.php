<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251116213600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, supplier_email VARCHAR(255) NOT NULL, date_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "orders" (id SERIAL NOT NULL, subscription_package_id INT DEFAULT NULL, order_number VARCHAR(255) NOT NULL, customer_phone_number VARCHAR(255) NOT NULL, order_status VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, date_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E52FFDEE36A9EB9A ON "orders" (subscription_package_id)');
        $this->addSql('CREATE TABLE order_article (order_id INT NOT NULL, article_id INT NOT NULL, PRIMARY KEY(order_id, article_id))');
        $this->addSql('CREATE INDEX IDX_F440A72D8D9F6D38 ON order_article (order_id)');
        $this->addSql('CREATE INDEX IDX_F440A72D7294869C ON order_article (article_id)');
        $this->addSql('CREATE TABLE subscription_package (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, includes_monthly_magazine BOOLEAN NOT NULL, date_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE "orders" ADD CONSTRAINT FK_E52FFDEE36A9EB9A FOREIGN KEY (subscription_package_id) REFERENCES subscription_package (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_article ADD CONSTRAINT FK_F440A72D8D9F6D38 FOREIGN KEY (order_id) REFERENCES "orders" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_article ADD CONSTRAINT FK_F440A72D7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "orders" DROP CONSTRAINT FK_E52FFDEE36A9EB9A');
        $this->addSql('ALTER TABLE order_article DROP CONSTRAINT FK_F440A72D8D9F6D38');
        $this->addSql('ALTER TABLE order_article DROP CONSTRAINT FK_F440A72D7294869C');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE "orders"');
        $this->addSql('DROP TABLE order_article');
        $this->addSql('DROP TABLE subscription_package');
    }
}
