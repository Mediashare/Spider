<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190902171243 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE web_spider DROP FOREIGN KEY FK_7DADC7C324DB0683');
        $this->addSql('ALTER TABLE href DROP FOREIGN KEY FK_34F8E741953C1C61');
        $this->addSql('ALTER TABLE meta_data DROP FOREIGN KEY FK_3E558020312139BC');
        $this->addSql('ALTER TABLE search DROP FOREIGN KEY FK_B4F0DBA781CFDAE7');
        $this->addSql('ALTER TABLE url DROP FOREIGN KEY FK_F47645AE312139BC');
        $this->addSql('ALTER TABLE crawled DROP FOREIGN KEY FK_20FAEBF32EF91FD8');
        $this->addSql('ALTER TABLE crawled DROP FOREIGN KEY FK_20FAEBF33CD4754E');
        $this->addSql('ALTER TABLE crawled DROP FOREIGN KEY FK_20FAEBF3D9747388');
        $this->addSql('ALTER TABLE url DROP FOREIGN KEY FK_F47645AED9747388');
        $this->addSql('DROP TABLE config');
        $this->addSql('DROP TABLE crawled');
        $this->addSql('DROP TABLE header');
        $this->addSql('DROP TABLE href');
        $this->addSql('DROP TABLE html');
        $this->addSql('DROP TABLE meta_data');
        $this->addSql('DROP TABLE search');
        $this->addSql('DROP TABLE url');
        $this->addSql('DROP TABLE web_spider');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('ALTER TABLE user DROP username, DROP create_date, DROP update_date');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE config (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, path_exceptions LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:array)\', custom_search LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:array)\', php_errors LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:array)\', create_date DATETIME NOT NULL, update_date DATETIME NOT NULL, INDEX IDX_D48A2F7CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE crawled (id INT AUTO_INCREMENT NOT NULL, web_spider_id INT NOT NULL, header_id INT DEFAULT NULL, html_id INT DEFAULT NULL, create_date DATETIME NOT NULL, update_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_20FAEBF33CD4754E (html_id), INDEX IDX_20FAEBF3D9747388 (web_spider_id), UNIQUE INDEX UNIQ_20FAEBF32EF91FD8 (header_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE header (id INT AUTO_INCREMENT NOT NULL, http_code VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, header LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:array)\', create_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE href (id INT AUTO_INCREMENT NOT NULL, source_id INT NOT NULL, url VARCHAR(1500) NOT NULL COLLATE utf8mb4_unicode_ci, create_date DATETIME NOT NULL, INDEX IDX_34F8E741953C1C61 (source_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE html (id INT AUTO_INCREMENT NOT NULL, html LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, dom LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci COMMENT \'(DC2Type:object)\', create_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE meta_data (id INT AUTO_INCREMENT NOT NULL, crawled_id INT NOT NULL, title VARCHAR(1500) DEFAULT NULL COLLATE utf8mb4_unicode_ci, description LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_3E558020312139BC (crawled_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE search (id INT AUTO_INCREMENT NOT NULL, url_id INT NOT NULL, text VARCHAR(1500) NOT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_B4F0DBA781CFDAE7 (url_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE url (id INT AUTO_INCREMENT NOT NULL, crawled_id INT DEFAULT NULL, web_spider_id INT DEFAULT NULL, url VARCHAR(1500) NOT NULL COLLATE utf8mb4_unicode_ci, create_date DATETIME NOT NULL, update_date DATETIME NOT NULL, invalid TINYINT(1) NOT NULL, INDEX IDX_F47645AED9747388 (web_spider_id), UNIQUE INDEX UNIQ_F47645AE312139BC (crawled_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE web_spider (id INT AUTO_INCREMENT NOT NULL, config_id INT NOT NULL, web_spider TINYINT(1) NOT NULL, verbose TINYINT(1) NOT NULL, mod_cli TINYINT(1) NOT NULL, url_index VARCHAR(1500) NOT NULL COLLATE utf8mb4_unicode_ci, run TINYINT(1) NOT NULL, create_date DATETIME NOT NULL, update_date DATETIME NOT NULL, counter INT NOT NULL, INDEX IDX_7DADC7C324DB0683 (config_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE config ADD CONSTRAINT FK_D48A2F7CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE crawled ADD CONSTRAINT FK_20FAEBF32EF91FD8 FOREIGN KEY (header_id) REFERENCES header (id)');
        $this->addSql('ALTER TABLE crawled ADD CONSTRAINT FK_20FAEBF33CD4754E FOREIGN KEY (html_id) REFERENCES html (id)');
        $this->addSql('ALTER TABLE crawled ADD CONSTRAINT FK_20FAEBF3D9747388 FOREIGN KEY (web_spider_id) REFERENCES web_spider (id)');
        $this->addSql('ALTER TABLE href ADD CONSTRAINT FK_34F8E741953C1C61 FOREIGN KEY (source_id) REFERENCES crawled (id)');
        $this->addSql('ALTER TABLE meta_data ADD CONSTRAINT FK_3E558020312139BC FOREIGN KEY (crawled_id) REFERENCES crawled (id)');
        $this->addSql('ALTER TABLE search ADD CONSTRAINT FK_B4F0DBA781CFDAE7 FOREIGN KEY (url_id) REFERENCES crawled (id)');
        $this->addSql('ALTER TABLE url ADD CONSTRAINT FK_F47645AE312139BC FOREIGN KEY (crawled_id) REFERENCES crawled (id)');
        $this->addSql('ALTER TABLE url ADD CONSTRAINT FK_F47645AED9747388 FOREIGN KEY (web_spider_id) REFERENCES web_spider (id)');
        $this->addSql('ALTER TABLE web_spider ADD CONSTRAINT FK_7DADC7C324DB0683 FOREIGN KEY (config_id) REFERENCES config (id)');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD create_date DATETIME NOT NULL, ADD update_date DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }
}
