<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191207061513 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE stocks (id INT AUTO_INCREMENT NOT NULL, produit_id INT NOT NULL, projet_id INT DEFAULT NULL, mouvement_id INT NOT NULL, client_id INT DEFAULT NULL, unite_id INT NOT NULL, operateur_id INT NOT NULL, piece_id INT DEFAULT NULL, validation_id INT DEFAULT NULL, validateur_id INT DEFAULT NULL, stock_id INT DEFAULT NULL, etat_id INT NOT NULL, autre_source_id INT DEFAULT NULL, quantite DOUBLE PRECISION NOT NULL, cause_annulation LONGTEXT DEFAULT NULL, date_saisie DATETIME NOT NULL, date_validation DATETIME DEFAULT NULL, reference_panier VARCHAR(255) NOT NULL, site VARCHAR(255) DEFAULT NULL, remarque LONGTEXT DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, INDEX IDX_56F79805F347EFB (produit_id), INDEX IDX_56F79805C18272 (projet_id), INDEX IDX_56F79805ECD1C222 (mouvement_id), INDEX IDX_56F7980519EB6921 (client_id), INDEX IDX_56F79805EC4A74AB (unite_id), INDEX IDX_56F798053F192FC (operateur_id), INDEX IDX_56F79805C40FCFA8 (piece_id), INDEX IDX_56F79805A2274850 (validation_id), INDEX IDX_56F79805E57AEF2F (validateur_id), INDEX IDX_56F79805DCD6110 (stock_id), INDEX IDX_56F79805D5E86FF (etat_id), INDEX IDX_56F79805EA4FA6CD (autre_source_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE clients (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) DEFAULT NULL, tel VARCHAR(255) DEFAULT NULL, mail VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE access (id INT AUTO_INCREMENT NOT NULL, access VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE autorisations (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, access_id INT NOT NULL, INDEX IDX_3AB39F8FA76ED395 (user_id), INDEX IDX_3AB39F8F4FEA67CF (access_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE conversions (id INT AUTO_INCREMENT NOT NULL, unitesource_id INT NOT NULL, unitesdestinataire_id INT NOT NULL, produits_id INT DEFAULT NULL, valeur DOUBLE PRECISION NOT NULL, INDEX IDX_6A02DBA5F499C212 (unitesource_id), INDEX IDX_6A02DBA5A7D7375E (unitesdestinataire_id), INDEX IDX_6A02DBA5CD11A2CF (produits_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etats (id INT AUTO_INCREMENT NOT NULL, etat VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lieu (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mouvements (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE piece_jointe (id INT AUTO_INCREMENT NOT NULL, nom_fichier VARCHAR(255) NOT NULL, nom_server VARCHAR(255) NOT NULL, reference VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produits (id INT AUTO_INCREMENT NOT NULL, produit VARCHAR(255) NOT NULL, designation VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_BE2DDF8C29A5EC27 (produit), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projet (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, nom VARCHAR(255) NOT NULL, reference VARCHAR(255) DEFAULT NULL, lieu VARCHAR(255) DEFAULT NULL, site VARCHAR(255) DEFAULT NULL, INDEX IDX_50159CA919EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_produits (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unites (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, sigle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, access_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, login VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, INDEX IDX_8D93D6494FEA67CF (access_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE username (id INT AUTO_INCREMENT NOT NULL, access_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_F85E0677F85E0677 (username), INDEX IDX_F85E06774FEA67CF (access_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE validations (id INT AUTO_INCREMENT NOT NULL, validation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stocks ADD CONSTRAINT FK_56F79805F347EFB FOREIGN KEY (produit_id) REFERENCES produits (id)');
        $this->addSql('ALTER TABLE stocks ADD CONSTRAINT FK_56F79805C18272 FOREIGN KEY (projet_id) REFERENCES projet (id)');
        $this->addSql('ALTER TABLE stocks ADD CONSTRAINT FK_56F79805ECD1C222 FOREIGN KEY (mouvement_id) REFERENCES mouvements (id)');
        $this->addSql('ALTER TABLE stocks ADD CONSTRAINT FK_56F7980519EB6921 FOREIGN KEY (client_id) REFERENCES clients (id)');
        $this->addSql('ALTER TABLE stocks ADD CONSTRAINT FK_56F79805EC4A74AB FOREIGN KEY (unite_id) REFERENCES unites (id)');
        $this->addSql('ALTER TABLE stocks ADD CONSTRAINT FK_56F798053F192FC FOREIGN KEY (operateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE stocks ADD CONSTRAINT FK_56F79805C40FCFA8 FOREIGN KEY (piece_id) REFERENCES piece_jointe (id)');
        $this->addSql('ALTER TABLE stocks ADD CONSTRAINT FK_56F79805A2274850 FOREIGN KEY (validation_id) REFERENCES validations (id)');
        $this->addSql('ALTER TABLE stocks ADD CONSTRAINT FK_56F79805E57AEF2F FOREIGN KEY (validateur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE stocks ADD CONSTRAINT FK_56F79805DCD6110 FOREIGN KEY (stock_id) REFERENCES stocks (id)');
        $this->addSql('ALTER TABLE stocks ADD CONSTRAINT FK_56F79805D5E86FF FOREIGN KEY (etat_id) REFERENCES etats (id)');
        $this->addSql('ALTER TABLE stocks ADD CONSTRAINT FK_56F79805EA4FA6CD FOREIGN KEY (autre_source_id) REFERENCES projet (id)');
        $this->addSql('ALTER TABLE autorisations ADD CONSTRAINT FK_3AB39F8FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE autorisations ADD CONSTRAINT FK_3AB39F8F4FEA67CF FOREIGN KEY (access_id) REFERENCES access (id)');
        $this->addSql('ALTER TABLE conversions ADD CONSTRAINT FK_6A02DBA5F499C212 FOREIGN KEY (unitesource_id) REFERENCES unites (id)');
        $this->addSql('ALTER TABLE conversions ADD CONSTRAINT FK_6A02DBA5A7D7375E FOREIGN KEY (unitesdestinataire_id) REFERENCES unites (id)');
        $this->addSql('ALTER TABLE conversions ADD CONSTRAINT FK_6A02DBA5CD11A2CF FOREIGN KEY (produits_id) REFERENCES produits (id)');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA919EB6921 FOREIGN KEY (client_id) REFERENCES clients (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494FEA67CF FOREIGN KEY (access_id) REFERENCES access (id)');
        $this->addSql('ALTER TABLE username ADD CONSTRAINT FK_F85E06774FEA67CF FOREIGN KEY (access_id) REFERENCES access (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stocks DROP FOREIGN KEY FK_56F79805DCD6110');
        $this->addSql('ALTER TABLE stocks DROP FOREIGN KEY FK_56F7980519EB6921');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA919EB6921');
        $this->addSql('ALTER TABLE autorisations DROP FOREIGN KEY FK_3AB39F8F4FEA67CF');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494FEA67CF');
        $this->addSql('ALTER TABLE username DROP FOREIGN KEY FK_F85E06774FEA67CF');
        $this->addSql('ALTER TABLE stocks DROP FOREIGN KEY FK_56F79805D5E86FF');
        $this->addSql('ALTER TABLE stocks DROP FOREIGN KEY FK_56F79805ECD1C222');
        $this->addSql('ALTER TABLE stocks DROP FOREIGN KEY FK_56F79805C40FCFA8');
        $this->addSql('ALTER TABLE stocks DROP FOREIGN KEY FK_56F79805F347EFB');
        $this->addSql('ALTER TABLE conversions DROP FOREIGN KEY FK_6A02DBA5CD11A2CF');
        $this->addSql('ALTER TABLE stocks DROP FOREIGN KEY FK_56F79805C18272');
        $this->addSql('ALTER TABLE stocks DROP FOREIGN KEY FK_56F79805EA4FA6CD');
        $this->addSql('ALTER TABLE stocks DROP FOREIGN KEY FK_56F79805EC4A74AB');
        $this->addSql('ALTER TABLE conversions DROP FOREIGN KEY FK_6A02DBA5F499C212');
        $this->addSql('ALTER TABLE conversions DROP FOREIGN KEY FK_6A02DBA5A7D7375E');
        $this->addSql('ALTER TABLE stocks DROP FOREIGN KEY FK_56F798053F192FC');
        $this->addSql('ALTER TABLE stocks DROP FOREIGN KEY FK_56F79805E57AEF2F');
        $this->addSql('ALTER TABLE autorisations DROP FOREIGN KEY FK_3AB39F8FA76ED395');
        $this->addSql('ALTER TABLE stocks DROP FOREIGN KEY FK_56F79805A2274850');
        $this->addSql('DROP TABLE stocks');
        $this->addSql('DROP TABLE clients');
        $this->addSql('DROP TABLE access');
        $this->addSql('DROP TABLE autorisations');
        $this->addSql('DROP TABLE conversions');
        $this->addSql('DROP TABLE etats');
        $this->addSql('DROP TABLE lieu');
        $this->addSql('DROP TABLE mouvements');
        $this->addSql('DROP TABLE piece_jointe');
        $this->addSql('DROP TABLE produits');
        $this->addSql('DROP TABLE projet');
        $this->addSql('DROP TABLE type_produits');
        $this->addSql('DROP TABLE unites');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE username');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE validations');
    }
}
