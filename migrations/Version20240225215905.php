<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240225215905 extends AbstractMigration
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
        $this->addSql('ALTER TABLE user ADD COLUMN pays_compte VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, titre, prix, disponibilite, image, article_type, auteur, isbn, nb_pages, date_de_parution, artiste FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, disponibilite INTEGER NOT NULL, image VARCHAR(255) NOT NULL, article_type VARCHAR(255) NOT NULL, auteur VARCHAR(255) DEFAULT NULL, isbn VARCHAR(255) DEFAULT NULL, nb_pages INTEGER DEFAULT NULL, date_de_parution VARCHAR(255) DEFAULT NULL, artiste VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO article (id, titre, prix, disponibilite, image, article_type, auteur, isbn, nb_pages, date_de_parution, artiste) SELECT id, titre, prix, disponibilite, image, article_type, auteur, isbn, nb_pages, date_de_parution, artiste FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, email, nom_compte, prenom_compte, num_tel_compte, adresse_postale_compte, code_postal_compte, ville_compte, activation_token, is_admin, reset_token, roles, password, is_verified FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, nom_compte VARCHAR(255) DEFAULT NULL, prenom_compte VARCHAR(255) DEFAULT NULL, num_tel_compte VARCHAR(255) DEFAULT NULL, adresse_postale_compte VARCHAR(255) DEFAULT NULL, code_postal_compte INTEGER DEFAULT NULL, ville_compte VARCHAR(255) DEFAULT NULL, activation_token VARCHAR(255) DEFAULT NULL, is_admin BOOLEAN NOT NULL, reset_token VARCHAR(100) DEFAULT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO user (id, email, nom_compte, prenom_compte, num_tel_compte, adresse_postale_compte, code_postal_compte, ville_compte, activation_token, is_admin, reset_token, roles, password, is_verified) SELECT id, email, nom_compte, prenom_compte, num_tel_compte, adresse_postale_compte, code_postal_compte, ville_compte, activation_token, is_admin, reset_token, roles, password, is_verified FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }
}
