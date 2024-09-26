<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240926090814 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation ADD agence_id INT NOT NULL, ADD user_id INT NOT NULL, ADD voiture_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955181A8BA FOREIGN KEY (voiture_id) REFERENCES voitures (id)');
        $this->addSql('CREATE INDEX IDX_42C84955D725330D ON reservation (agence_id)');
        $this->addSql('CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)');
        $this->addSql('CREATE INDEX IDX_42C84955181A8BA ON reservation (voiture_id)');
        $this->addSql('ALTER TABLE voitures ADD agence_id INT NOT NULL, ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE voitures ADD CONSTRAINT FK_8B58301BD725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('CREATE INDEX IDX_8B58301BD725330D ON voitures (agence_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955D725330D');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955181A8BA');
        $this->addSql('DROP INDEX IDX_42C84955D725330D ON reservation');
        $this->addSql('DROP INDEX IDX_42C84955A76ED395 ON reservation');
        $this->addSql('DROP INDEX IDX_42C84955181A8BA ON reservation');
        $this->addSql('ALTER TABLE reservation DROP agence_id, DROP user_id, DROP voiture_id');
        $this->addSql('ALTER TABLE voitures DROP FOREIGN KEY FK_8B58301BD725330D');
        $this->addSql('DROP INDEX IDX_8B58301BD725330D ON voitures');
        $this->addSql('ALTER TABLE voitures DROP agence_id, DROP image');
    }
}
