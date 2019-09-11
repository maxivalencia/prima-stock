<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190909183034 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE stocks (id INT AUTO_INCREMENT NOT NULL, produit_id INT NOT NULL, projet_id INT DEFAULT NULL, mouvement_id INT NOT NULL, client_id INT DEFAULT NULL, unite_id INT NOT NULL, operateur_id INT NOT NULL, piece_id INT DEFAULT NULL, validation_id INT DEFAULT NULL, validateur_id INT DEFAULT NULL, stock_id INT DEFAULT NULL, etat_id INT NOT NULL, quantite DOUBLE PRECISION NOT NULL, cause_annulation LONGTEXT DEFAULT NULL, date_saisie DATETIME NOT NULL, date_validation DATETIME DEFAULT NULL, reference_panier VARCHAR(255) NOT NULL, INDEX IDX_56F79805F347EFB (produit_id), INDEX IDX_56F79805C18272 (projet_id), INDEX IDX_56F79805ECD1C222 (mouvement_id), INDEX IDX_56F7980519EB6921 (client_id), INDEX IDX_56F79805EC4A74AB (unite_id), INDEX IDX_56F798053F192FC (operateur_id), INDEX IDX_56F79805C40FCFA8 (piece_id), INDEX IDX_56F79805A2274850 (validation_id), INDEX IDX_56F79805E57AEF2F (validateur_id), INDEX IDX_56F79805DCD6110 (stock_id), INDEX IDX_56F79805D5E86FF (etat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
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
        $this->addSql('ALTER TABLE clients CHANGE adresse adresse VARCHAR(255) DEFAULT NULL, CHANGE tel tel VARCHAR(255) DEFAULT NULL, CHANGE mail mail VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE projet CHANGE reference reference VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stocks DROP FOREIGN KEY FK_56F79805DCD6110');
        $this->addSql('DROP TABLE stocks');
        $this->addSql('ALTER TABLE clients CHANGE adresse adresse VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE tel tel VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE mail mail VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE projet CHANGE reference reference VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
