<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191015013429 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE produits DROP FOREIGN KEY FK_BE2DDF8C1237A8DE');
        $this->addSql('DROP INDEX IDX_BE2DDF8C1237A8DE ON produits');
        $this->addSql('ALTER TABLE produits DROP type_produit_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BE2DDF8C29A5EC27 ON produits (produit)');
        $this->addSql('ALTER TABLE utilisateur CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE roles roles JSON NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE stocks ADD autre_source_id INT DEFAULT NULL, ADD site VARCHAR(255) DEFAULT NULL, ADD remarque LONGTEXT DEFAULT NULL, ADD reference VARCHAR(255) DEFAULT NULL, CHANGE projet_id projet_id INT DEFAULT NULL, CHANGE client_id client_id INT DEFAULT NULL, CHANGE piece_id piece_id INT DEFAULT NULL, CHANGE validation_id validation_id INT DEFAULT NULL, CHANGE validateur_id validateur_id INT DEFAULT NULL, CHANGE stock_id stock_id INT DEFAULT NULL, CHANGE date_validation date_validation DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE stocks ADD CONSTRAINT FK_56F79805EA4FA6CD FOREIGN KEY (autre_source_id) REFERENCES projet (id)');
        $this->addSql('CREATE INDEX IDX_56F79805EA4FA6CD ON stocks (autre_source_id)');
        $this->addSql('ALTER TABLE username CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE roles roles JSON NOT NULL, CHANGE access_id access_id INT DEFAULT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE username ADD CONSTRAINT FK_F85E06774FEA67CF FOREIGN KEY (access_id) REFERENCES access (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F85E0677F85E0677 ON username (username)');
        $this->addSql('CREATE INDEX IDX_F85E06774FEA67CF ON username (access_id)');
        $this->addSql('ALTER TABLE projet ADD lieu VARCHAR(255) DEFAULT NULL, ADD site VARCHAR(255) DEFAULT NULL, CHANGE reference reference VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD access_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6494FEA67CF FOREIGN KEY (access_id) REFERENCES access (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6494FEA67CF ON user (access_id)');
        $this->addSql('ALTER TABLE lieu CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE clients CHANGE adresse adresse VARCHAR(255) DEFAULT NULL, CHANGE tel tel VARCHAR(255) DEFAULT NULL, CHANGE mail mail VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE conversions ADD produits_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE conversions ADD CONSTRAINT FK_6A02DBA5CD11A2CF FOREIGN KEY (produits_id) REFERENCES produits (id)');
        $this->addSql('CREATE INDEX IDX_6A02DBA5CD11A2CF ON conversions (produits_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE clients CHANGE adresse adresse VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE tel tel VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE mail mail VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE conversions DROP FOREIGN KEY FK_6A02DBA5CD11A2CF');
        $this->addSql('DROP INDEX IDX_6A02DBA5CD11A2CF ON conversions');
        $this->addSql('ALTER TABLE conversions DROP produits_id');
        $this->addSql('ALTER TABLE lieu CHANGE id id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_BE2DDF8C29A5EC27 ON produits');
        $this->addSql('ALTER TABLE produits ADD type_produit_id INT NOT NULL');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT FK_BE2DDF8C1237A8DE FOREIGN KEY (type_produit_id) REFERENCES type_produits (id)');
        $this->addSql('CREATE INDEX IDX_BE2DDF8C1237A8DE ON produits (type_produit_id)');
        $this->addSql('ALTER TABLE projet DROP lieu, DROP site, CHANGE reference reference VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE stocks DROP FOREIGN KEY FK_56F79805EA4FA6CD');
        $this->addSql('DROP INDEX IDX_56F79805EA4FA6CD ON stocks');
        $this->addSql('ALTER TABLE stocks DROP autre_source_id, DROP site, DROP remarque, DROP reference, CHANGE projet_id projet_id INT DEFAULT NULL, CHANGE client_id client_id INT DEFAULT NULL, CHANGE piece_id piece_id INT DEFAULT NULL, CHANGE validation_id validation_id INT DEFAULT NULL, CHANGE validateur_id validateur_id INT DEFAULT NULL, CHANGE stock_id stock_id INT DEFAULT NULL, CHANGE date_validation date_validation DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6494FEA67CF');
        $this->addSql('DROP INDEX IDX_8D93D6494FEA67CF ON user');
        $this->addSql('ALTER TABLE user DROP access_id');
        $this->addSql('ALTER TABLE username MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE username DROP FOREIGN KEY FK_F85E06774FEA67CF');
        $this->addSql('DROP INDEX UNIQ_F85E0677F85E0677 ON username');
        $this->addSql('DROP INDEX IDX_F85E06774FEA67CF ON username');
        $this->addSql('ALTER TABLE username DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE username CHANGE id id INT NOT NULL, CHANGE access_id access_id INT DEFAULT NULL, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
        $this->addSql('ALTER TABLE utilisateur MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE utilisateur DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE utilisateur CHANGE id id INT NOT NULL, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
