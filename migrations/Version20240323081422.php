<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240323081422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notifications_stock (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, article_id INTEGER NOT NULL, CONSTRAINT FK_DB41749A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_DB417497294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_DB41749A76ED395 ON notifications_stock (user_id)');
        $this->addSql('CREATE INDEX IDX_DB417497294869C ON notifications_stock (article_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, titre, prix, disponibilite, image, article_type, auteur, genre, description, isbn, nb_pages, date_de_parution, artiste FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER NOT NULL, titre VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, disponibilite INTEGER NOT NULL, image VARCHAR(255) NOT NULL, article_type VARCHAR(255) NOT NULL, auteur VARCHAR(255) DEFAULT NULL, genre VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, isbn VARCHAR(255) DEFAULT NULL, nb_pages INTEGER DEFAULT NULL, date_de_parution VARCHAR(255) DEFAULT NULL, artiste VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO article (id, titre, prix, disponibilite, image, article_type, auteur, genre, description, isbn, nb_pages, date_de_parution, artiste) SELECT id, titre, prix, disponibilite, image, article_type, auteur, genre, description, isbn, nb_pages, date_de_parution, artiste FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE notifications_stock');
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, titre, prix, disponibilite, image, article_type, auteur, genre, description, isbn, nb_pages, date_de_parution, artiste FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, disponibilite INTEGER NOT NULL, image VARCHAR(255) NOT NULL, article_type VARCHAR(255) NOT NULL, auteur VARCHAR(255) DEFAULT NULL, genre VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, isbn VARCHAR(255) DEFAULT NULL, nb_pages INTEGER DEFAULT NULL, date_de_parution VARCHAR(255) DEFAULT NULL, artiste VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO article (id, titre, prix, disponibilite, image, article_type, auteur, genre, description, isbn, nb_pages, date_de_parution, artiste) SELECT id, titre, prix, disponibilite, image, article_type, auteur, genre, description, isbn, nb_pages, date_de_parution, artiste FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
    }
}
