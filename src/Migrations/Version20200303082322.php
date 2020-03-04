<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200303082322 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE date_value ADD parser_type VARCHAR(255) NOT NULL');
        // TODO add unique ix for code, for currency+date
        $this->addSql('ALTER TABLE currency ADD UNIQUE INDEX idx_currency_code (code)');
        $this->addSql('ALTER TABLE date_value ADD UNIQUE INDEX idx_date_value_currency_date (currency_id, date)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE date_value DROP parser_type');
        $this->addSql('ALTER TABLE currency DROP INDEX idx_currency_code');
        $this->addSql('ALTER TABLE date_value DROP INDEX idx_date_value_currency_date');
    }
}
