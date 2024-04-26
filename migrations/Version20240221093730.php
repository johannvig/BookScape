<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240221093730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande_article (commande_id INTEGER NOT NULL, article_id INTEGER NOT NULL, PRIMARY KEY(commande_id, article_id), CONSTRAINT FK_F4817CC682EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F4817CC67294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F4817CC682EA2E54 ON commande_article (commande_id)');
        $this->addSql('CREATE INDEX IDX_F4817CC67294869C ON commande_article (article_id)');
        $this->addSql('DROP TABLE article_commande');
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, titre, prix, disponibilite, image, article_type, auteur, isbn, nb_pages, date_de_parution, artiste FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER NOT NULL, titre VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, disponibilite INTEGER NOT NULL, image VARCHAR(255) NOT NULL, article_type VARCHAR(255) NOT NULL, auteur VARCHAR(255) DEFAULT NULL, isbn VARCHAR(255) DEFAULT NULL, nb_pages INTEGER DEFAULT NULL, date_de_parution VARCHAR(255) DEFAULT NULL, artiste VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO article (id, titre, prix, disponibilite, image, article_type, auteur, isbn, nb_pages, date_de_parution, artiste) SELECT id, titre, prix, disponibilite, image, article_type, auteur, isbn, nb_pages, date_de_parution, artiste FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
        $this->addSql('CREATE TEMPORARY TABLE __temp__avis AS SELECT id, compte_id, id_avis, note_avis, commentaire_avis, date_avis FROM avis');
        $this->addSql('DROP TABLE avis');
        $this->addSql('CREATE TABLE avis (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, compte_id INTEGER NOT NULL, article_id INTEGER NOT NULL, note_avis INTEGER NOT NULL, commentaire_avis CLOB DEFAULT NULL, date_avis DATETIME NOT NULL, CONSTRAINT FK_8F91ABF0F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8F91ABF07294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO avis (id, compte_id, article_id, note_avis, commentaire_avis, date_avis) SELECT id, compte_id, id_avis, note_avis, commentaire_avis, date_avis FROM __temp__avis');
        $this->addSql('DROP TABLE __temp__avis');
        $this->addSql('CREATE INDEX IDX_8F91ABF0F2C56620 ON avis (compte_id)');
        $this->addSql('CREATE INDEX IDX_8F91ABF07294869C ON avis (article_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__commande AS SELECT id, compte_id, date_commande, statut_commande FROM commande');
        $this->addSql('DROP TABLE commande');
        $this->addSql('CREATE TABLE commande (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, compte_id INTEGER NOT NULL, date_commande DATETIME NOT NULL, statut_commande VARCHAR(20) NOT NULL, CONSTRAINT FK_6EEAA67DF2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO commande (id, compte_id, date_commande, statut_commande) SELECT id, compte_id, date_commande, statut_commande FROM __temp__commande');
        $this->addSql('DROP TABLE __temp__commande');
        $this->addSql('CREATE INDEX IDX_6EEAA67DF2C56620 ON commande (compte_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__compte AS SELECT id, role_id, nom_compte, prenom_compte, adresse_email_compte, num_tel_compte, mot_passe_compte, adresse_postale_compte, code_postal_compte, ville_compte FROM compte');
        $this->addSql('DROP TABLE compte');
        $this->addSql('CREATE TABLE compte (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, role_id INTEGER NOT NULL, nom_compte VARCHAR(30) NOT NULL, prenom_compte VARCHAR(40) NOT NULL, adresse_email_compte VARCHAR(40) NOT NULL, num_tel_compte VARCHAR(20) DEFAULT NULL, mot_passe_compte VARCHAR(40) NOT NULL, adresse_postale_compte VARCHAR(40) DEFAULT NULL, code_postal_compte INTEGER DEFAULT NULL, ville_compte VARCHAR(40) DEFAULT NULL, CONSTRAINT FK_CFF65260D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO compte (id, role_id, nom_compte, prenom_compte, adresse_email_compte, num_tel_compte, mot_passe_compte, adresse_postale_compte, code_postal_compte, ville_compte) SELECT id, role_id, nom_compte, prenom_compte, adresse_email_compte, num_tel_compte, mot_passe_compte, adresse_postale_compte, code_postal_compte, ville_compte FROM __temp__compte');
        $this->addSql('DROP TABLE __temp__compte');
        $this->addSql('CREATE INDEX IDX_CFF65260D60322AC ON compte (role_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__role AS SELECT id, nom_role FROM role');
        $this->addSql('DROP TABLE role');
        $this->addSql('CREATE TABLE role (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom_role VARCHAR(30) NOT NULL)');
        $this->addSql('INSERT INTO role (id, nom_role) SELECT id, nom_role FROM __temp__role');
        $this->addSql('DROP TABLE __temp__role');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article_commande (article_id INTEGER NOT NULL, commande_id INTEGER NOT NULL, PRIMARY KEY(article_id, commande_id), CONSTRAINT FK_3B0252167294869C FOREIGN KEY (article_id) REFERENCES article (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_3B02521682EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_3B02521682EA2E54 ON article_commande (commande_id)');
        $this->addSql('CREATE INDEX IDX_3B0252167294869C ON article_commande (article_id)');
        $this->addSql('DROP TABLE commande_article');
        $this->addSql('CREATE TEMPORARY TABLE __temp__article AS SELECT id, titre, prix, disponibilite, image, article_type, auteur, isbn, nb_pages, date_de_parution, artiste FROM article');
        $this->addSql('DROP TABLE article');
        $this->addSql('CREATE TABLE article (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, disponibilite INTEGER NOT NULL, image VARCHAR(255) NOT NULL, article_type VARCHAR(255) NOT NULL, auteur VARCHAR(255) DEFAULT NULL, isbn VARCHAR(255) DEFAULT NULL, nb_pages INTEGER DEFAULT NULL, date_de_parution VARCHAR(255) DEFAULT NULL, artiste VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO article (id, titre, prix, disponibilite, image, article_type, auteur, isbn, nb_pages, date_de_parution, artiste) SELECT id, titre, prix, disponibilite, image, article_type, auteur, isbn, nb_pages, date_de_parution, artiste FROM __temp__article');
        $this->addSql('DROP TABLE __temp__article');
        $this->addSql('CREATE TEMPORARY TABLE __temp__avis AS SELECT id, compte_id, article_id, note_avis, commentaire_avis, date_avis FROM avis');
        $this->addSql('DROP TABLE avis');
        $this->addSql('CREATE TABLE avis (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, compte_id INTEGER NOT NULL, id_avis INTEGER NOT NULL, note_avis INTEGER NOT NULL, commentaire_avis CLOB DEFAULT NULL, date_avis DATETIME NOT NULL, CONSTRAINT FK_8F91ABF0F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO avis (id, compte_id, id_avis, note_avis, commentaire_avis, date_avis) SELECT id, compte_id, article_id, note_avis, commentaire_avis, date_avis FROM __temp__avis');
        $this->addSql('DROP TABLE __temp__avis');
        $this->addSql('CREATE INDEX IDX_8F91ABF0F2C56620 ON avis (compte_id)');
        $this->addSql('ALTER TABLE commande ADD COLUMN id_commande INTEGER NOT NULL');
        $this->addSql('ALTER TABLE compte ADD COLUMN id_compte INTEGER NOT NULL');
        $this->addSql('ALTER TABLE role ADD COLUMN id_role INTEGER NOT NULL');
    }
}
