<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240221083858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, titre, prix, disponibilite, image, article_type, auteur, isbn, nb_pages, date_de_parution, artiste FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER NOT NULL, titre VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, disponibilite INTEGER NOT NULL, image VARCHAR(255) NOT NULL, article_type VARCHAR(255) NOT NULL, auteur VARCHAR(255) DEFAULT NULL, isbn VARCHAR(255) DEFAULT NULL, nb_pages INTEGER DEFAULT NULL, date_de_parution VARCHAR(255) DEFAULT NULL, artiste VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO article (id, titre, prix, disponibilite, image, article_type, auteur, isbn, nb_pages, date_de_parution, artiste) SELECT id, titre, prix, disponibilite, image, article_type, auteur, isbn, nb_pages, date_de_parution, artiste FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
        $this->addSql('CREATE TEMPORARY TABLE __temp__commande AS SELECT id, id_commande, date_commande, statut_commande FROM commande');
        $this->addSql('DROP TABLE commande');
        $this->addSql('CREATE TABLE commande (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, compte_id INTEGER NOT NULL, id_commande INTEGER NOT NULL, date_commande DATETIME NOT NULL, statut_commande VARCHAR(20) NOT NULL, CONSTRAINT FK_6EEAA67DF2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO commande (id, id_commande, date_commande, statut_commande) SELECT id, id_commande, date_commande, statut_commande FROM __temp__commande');
        $this->addSql('DROP TABLE __temp__commande');
        $this->addSql('CREATE INDEX IDX_6EEAA67DF2C56620 ON commande (compte_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, titre, prix, disponibilite, image, article_type, auteur, isbn, nb_pages, date_de_parution, artiste FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, disponibilite INTEGER NOT NULL, image VARCHAR(255) NOT NULL, article_type VARCHAR(255) NOT NULL, auteur VARCHAR(255) DEFAULT NULL, isbn VARCHAR(255) DEFAULT NULL, nb_pages INTEGER DEFAULT NULL, date_de_parution VARCHAR(255) DEFAULT NULL, artiste VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO article (id, titre, prix, disponibilite, image, article_type, auteur, isbn, nb_pages, date_de_parution, artiste) SELECT id, titre, prix, disponibilite, image, article_type, auteur, isbn, nb_pages, date_de_parution, artiste FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
        $this->addSql('CREATE TEMPORARY TABLE __temp__commande AS SELECT id, id_commande, date_commande, statut_commande FROM commande');
        $this->addSql('DROP TABLE commande');
        $this->addSql('CREATE TABLE commande (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, id_commande INTEGER NOT NULL, date_commande DATETIME NOT NULL, statut_commande VARCHAR(20) NOT NULL)');
        $this->addSql('INSERT INTO commande (id, id_commande, date_commande, statut_commande) SELECT id, id_commande, date_commande, statut_commande FROM __temp__commande');
        $this->addSql('DROP TABLE __temp__commande');
    }
}
