<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261003094207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cita_servicio ADD cita_id INT NOT NULL');
        $this->addSql('ALTER TABLE cita_servicio ADD servicio_id INT NOT NULL');
        $this->addSql('ALTER TABLE cita_servicio ADD CONSTRAINT FK_7A274B501E011DDF FOREIGN KEY (cita_id) REFERENCES cita (id) NOT DEFERRABLE');
        $this->addSql('ALTER TABLE cita_servicio ADD CONSTRAINT FK_7A274B5071CAA3E7 FOREIGN KEY (servicio_id) REFERENCES servicio (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_7A274B501E011DDF ON cita_servicio (cita_id)');
        $this->addSql('CREATE INDEX IDX_7A274B5071CAA3E7 ON cita_servicio (servicio_id)');
        $this->addSql('ALTER TABLE servicio_tarifa ADD servicio_id INT NOT NULL');
        $this->addSql('ALTER TABLE servicio_tarifa ADD CONSTRAINT FK_5F4FA0A871CAA3E7 FOREIGN KEY (servicio_id) REFERENCES servicio (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_5F4FA0A871CAA3E7 ON servicio_tarifa (servicio_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cita_servicio DROP CONSTRAINT FK_7A274B501E011DDF');
        $this->addSql('ALTER TABLE cita_servicio DROP CONSTRAINT FK_7A274B5071CAA3E7');
        $this->addSql('DROP INDEX IDX_7A274B501E011DDF');
        $this->addSql('DROP INDEX IDX_7A274B5071CAA3E7');
        $this->addSql('ALTER TABLE cita_servicio DROP cita_id');
        $this->addSql('ALTER TABLE cita_servicio DROP servicio_id');
        $this->addSql('ALTER TABLE servicio_tarifa DROP CONSTRAINT FK_5F4FA0A871CAA3E7');
        $this->addSql('DROP INDEX IDX_5F4FA0A871CAA3E7');
        $this->addSql('ALTER TABLE servicio_tarifa DROP servicio_id');
    }
}
