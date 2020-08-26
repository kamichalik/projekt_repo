<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200823091032 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('INSERT INTO user (email, password, roles) VALUES("admin@admin.pl", :password, \'[\"admin\"]\')',
            [':password' => 'hasloadmin']);

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
