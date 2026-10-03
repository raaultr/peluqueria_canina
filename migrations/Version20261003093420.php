<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261003093420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cita ADD trabajador_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cita ADD mascota_id INT NOT NULL');
        $this->addSql('ALTER TABLE cita ADD CONSTRAINT FK_3E379A62EC3656E FOREIGN KEY (trabajador_id) REFERENCES "user" (id)');
        $this->addSql('ALTER TABLE cita ADD CONSTRAINT FK_3E379A62FB60C59E FOREIGN KEY (mascota_id) REFERENCES mascota (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_3E379A62EC3656E ON cita (trabajador_id)');
        $this->addSql('CREATE INDEX IDX_3E379A62FB60C59E ON cita (mascota_id)');
        $this->addSql('ALTER TABLE mascota ADD cliente_id INT NOT NULL');
        $this->addSql('ALTER TABLE mascota ADD CONSTRAINT FK_11298D77DE734E51 FOREIGN KEY (cliente_id) REFERENCES "user" (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_11298D77DE734E51 ON mascota (cliente_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cita DROP CONSTRAINT FK_3E379A62EC3656E');
        $this->addSql('ALTER TABLE cita DROP CONSTRAINT FK_3E379A62FB60C59E');
        $this->addSql('DROP INDEX IDX_3E379A62EC3656E');
        $this->addSql('DROP INDEX IDX_3E379A62FB60C59E');
        $this->addSql('ALTER TABLE cita DROP trabajador_id');
        $this->addSql('ALTER TABLE cita DROP mascota_id');
        $this->addSql('ALTER TABLE mascota DROP CONSTRAINT FK_11298D77DE734E51');
        $this->addSql('DROP INDEX IDX_11298D77DE734E51');
        $this->addSql('ALTER TABLE mascota DROP cliente_id');
    }
}
