<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250610152401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE approval (id INT AUTO_INCREMENT NOT NULL, cart_id INT NOT NULL, admin_id INT DEFAULT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_16E0952B1AD5CDBF (cart_id), INDEX IDX_16E0952B642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, medicament_id INT NOT NULL, quantity INT NOT NULL, requires_approval TINYINT(1) NOT NULL, INDEX IDX_BA388B7A76ED395 (user_id), INDEX IDX_BA388B7AB0D61F7 (medicament_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE approval ADD CONSTRAINT FK_16E0952B1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id)');
        $this->addSql('ALTER TABLE approval ADD CONSTRAINT FK_16E0952B642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE cart ADD CONSTRAINT FK_BA388B7AB0D61F7 FOREIGN KEY (medicament_id) REFERENCES medicament (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE approval DROP FOREIGN KEY FK_16E0952B1AD5CDBF');
        $this->addSql('ALTER TABLE approval DROP FOREIGN KEY FK_16E0952B642B8210');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7A76ED395');
        $this->addSql('ALTER TABLE cart DROP FOREIGN KEY FK_BA388B7AB0D61F7');
        $this->addSql('DROP TABLE approval');
        $this->addSql('DROP TABLE cart');
    }
}
