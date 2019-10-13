<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191013085301 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_BE2DDF8C29A5EC27 ON produits (produit)');
        $this->addSql('ALTER TABLE conversions CHANGE produits_id produits_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE access_id access_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE projet CHANGE reference reference VARCHAR(255) DEFAULT NULL, CHANGE lieu lieu VARCHAR(255) DEFAULT NULL, CHANGE site site VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE username CHANGE access_id access_id INT DEFAULT NULL, CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE utilisateur CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE clients CHANGE adresse adresse VARCHAR(255) DEFAULT NULL, CHANGE tel tel VARCHAR(255) DEFAULT NULL, CHANGE mail mail VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE stocks CHANGE projet_id projet_id INT DEFAULT NULL, CHANGE client_id client_id INT DEFAULT NULL, CHANGE piece_id piece_id INT DEFAULT NULL, CHANGE validation_id validation_id INT DEFAULT NULL, CHANGE validateur_id validateur_id INT DEFAULT NULL, CHANGE stock_id stock_id INT DEFAULT NULL, CHANGE autre_source_id autre_source_id INT DEFAULT NULL, CHANGE date_validation date_validation DATETIME DEFAULT NULL, CHANGE site site VARCHAR(255) DEFAULT NULL, CHANGE reference reference VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE clients CHANGE adresse adresse VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE tel tel VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE mail mail VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE conversions CHANGE produits_id produits_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_BE2DDF8C29A5EC27 ON produits');
        $this->addSql('ALTER TABLE projet CHANGE reference reference VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE lieu lieu VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE site site VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE stocks CHANGE projet_id projet_id INT DEFAULT NULL, CHANGE client_id client_id INT DEFAULT NULL, CHANGE piece_id piece_id INT DEFAULT NULL, CHANGE validation_id validation_id INT DEFAULT NULL, CHANGE validateur_id validateur_id INT DEFAULT NULL, CHANGE stock_id stock_id INT DEFAULT NULL, CHANGE autre_source_id autre_source_id INT DEFAULT NULL, CHANGE date_validation date_validation DATETIME DEFAULT \'NULL\', CHANGE site site VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE reference reference VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user CHANGE access_id access_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE username CHANGE access_id access_id INT DEFAULT NULL, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
        $this->addSql('ALTER TABLE utilisateur CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
    }
}
