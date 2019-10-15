<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190909175011 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE conversions (id INT AUTO_INCREMENT NOT NULL, unitesource_id INT NOT NULL, unitesdestinataire_id INT NOT NULL, valeur DOUBLE PRECISION NOT NULL, INDEX IDX_6A02DBA5F499C212 (unitesource_id), INDEX IDX_6A02DBA5A7D7375E (unitesdestinataire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE conversions ADD CONSTRAINT FK_6A02DBA5F499C212 FOREIGN KEY (unitesource_id) REFERENCES unites (id)');
        $this->addSql('ALTER TABLE conversions ADD CONSTRAINT FK_6A02DBA5A7D7375E FOREIGN KEY (unitesdestinataire_id) REFERENCES unites (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE conversions');
    }
}
