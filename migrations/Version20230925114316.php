<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230925114316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE manufacturers_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE models_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE manufacturers (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE models (id INT NOT NULL, manufacturer_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E4D63009A23B42D ON models (manufacturer_id)');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('ALTER TABLE models ADD CONSTRAINT FK_E4D63009A23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cars ADD manufacturer_id INT NOT NULL');
        $this->addSql('ALTER TABLE cars ADD model_id INT NOT NULL');
        $this->addSql('ALTER TABLE cars ADD creator_id INT NOT NULL');
        $this->addSql('ALTER TABLE cars ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE cars ADD year INT NOT NULL');
        $this->addSql('ALTER TABLE cars DROP brand');
        $this->addSql('ALTER TABLE cars DROP model');
        $this->addSql('ALTER TABLE cars DROP color');
        $this->addSql('ALTER TABLE cars ADD CONSTRAINT FK_95C71D14A23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cars ADD CONSTRAINT FK_95C71D147975B7E7 FOREIGN KEY (model_id) REFERENCES models (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE cars ADD CONSTRAINT FK_95C71D1461220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_95C71D14A23B42D ON cars (manufacturer_id)');
        $this->addSql('CREATE INDEX IDX_95C71D147975B7E7 ON cars (model_id)');
        $this->addSql('CREATE INDEX IDX_95C71D1461220EA6 ON cars (creator_id)');
        $this->addSql('ALTER TABLE reviews ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6970EB0FA76ED395 ON reviews (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE cars DROP CONSTRAINT FK_95C71D14A23B42D');
        $this->addSql('ALTER TABLE cars DROP CONSTRAINT FK_95C71D147975B7E7');
        $this->addSql('ALTER TABLE cars DROP CONSTRAINT FK_95C71D1461220EA6');
        $this->addSql('ALTER TABLE reviews DROP CONSTRAINT FK_6970EB0FA76ED395');
        $this->addSql('DROP SEQUENCE manufacturers_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE models_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('ALTER TABLE models DROP CONSTRAINT FK_E4D63009A23B42D');
        $this->addSql('DROP TABLE manufacturers');
        $this->addSql('DROP TABLE models');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP INDEX IDX_95C71D14A23B42D');
        $this->addSql('DROP INDEX IDX_95C71D147975B7E7');
        $this->addSql('DROP INDEX IDX_95C71D1461220EA6');
        $this->addSql('ALTER TABLE cars ADD model VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE cars ADD color VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE cars DROP manufacturer_id');
        $this->addSql('ALTER TABLE cars DROP model_id');
        $this->addSql('ALTER TABLE cars DROP creator_id');
        $this->addSql('ALTER TABLE cars DROP year');
        $this->addSql('ALTER TABLE cars RENAME COLUMN name TO brand');
        $this->addSql('DROP INDEX IDX_6970EB0FA76ED395');
        $this->addSql('ALTER TABLE reviews DROP user_id');
    }
}
