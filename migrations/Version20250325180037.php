<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250325180037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id SERIAL NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE news (id SERIAL NOT NULL, title VARCHAR(255) NOT NULL, short_description TEXT NOT NULL, content TEXT NOT NULL, insert_date DATE NOT NULL, picture VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE news_category (news_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(news_id, category_id))');
        $this->addSql('CREATE INDEX IDX_4F72BA90B5A459A0 ON news_category (news_id)');
        $this->addSql('CREATE INDEX IDX_4F72BA9012469DE2 ON news_category (category_id)');
        $this->addSql('ALTER TABLE news_category ADD CONSTRAINT FK_4F72BA90B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE news_category ADD CONSTRAINT FK_4F72BA9012469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE news_category DROP CONSTRAINT FK_4F72BA90B5A459A0');
        $this->addSql('ALTER TABLE news_category DROP CONSTRAINT FK_4F72BA9012469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE news_category');
    }
}
