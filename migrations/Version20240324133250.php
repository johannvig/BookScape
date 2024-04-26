<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240324133250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande_pdf (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, commande_id INTEGER NOT NULL, pdf_content BLOB NOT NULL, created_at DATETIME NOT NULL, CONSTRAINT FK_9A0CCDC82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9A0CCDC82EA2E54 ON commande_pdf (commande_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, titre, prix, disponibilite, image, article_type, auteur, genre, description, isbn, nb_pages, date_de_parution, artiste FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER NOT NULL, titre VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, disponibilite INTEGER NOT NULL, image VARCHAR(255) NOT NULL, article_type VARCHAR(255) NOT NULL, auteur VARCHAR(255) DEFAULT NULL, genre VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, isbn VARCHAR(255) DEFAULT NULL, nb_pages INTEGER DEFAULT NULL, date_de_parution VARCHAR(255) DEFAULT NULL, artiste VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO article (id, titre, prix, disponibilite, image, article_type, auteur, genre, description, isbn, nb_pages, date_de_parution, artiste) SELECT id, titre, prix, disponibilite, image, article_type, auteur, genre, description, isbn, nb_pages, date_de_parution, artiste FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE commande_pdf');
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, titre, prix, disponibilite, image, article_type, auteur, genre, description, isbn, nb_pages, date_de_parution, artiste FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, disponibilite INTEGER NOT NULL, image VARCHAR(255) NOT NULL, article_type VARCHAR(255) NOT NULL, auteur VARCHAR(255) DEFAULT NULL, genre VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, isbn VARCHAR(255) DEFAULT NULL, nb_pages INTEGER DEFAULT NULL, date_de_parution VARCHAR(255) DEFAULT NULL, artiste VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO article (id, titre, prix, disponibilite, image, article_type, auteur, genre, description, isbn, nb_pages, date_de_parution, artiste) SELECT id, titre, prix, disponibilite, image, article_type, auteur, genre, description, isbn, nb_pages, date_de_parution, artiste FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
    }
}
