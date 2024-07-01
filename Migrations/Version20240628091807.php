<?php

declare(strict_types=1);

/*
 * This file is part of the "AarhusKommuneBundle" for Kimai.
 * All rights reserved by ITK Development (https://github.com/itk-kimai).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KimaiPlugin\AarhusKommuneBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240628091807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Set user defaults on existing users';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE kimai2_user_preferences SET value = 'intro,profile' WHERE name = '__wizards__'");
        $this->addSql("UPDATE kimai2_user_preferences SET value = 'da' WHERE name = 'language'");
        $this->addSql("UPDATE kimai2_user_preferences SET value = 'da' WHERE name = 'locale'");
        $this->addSql("UPDATE kimai2_user_preferences SET value = 'quick_entry' WHERE name = 'login_initial_view'");
        $this->addSql("UPDATE kimai2_user_preferences SET value = 'defaults' WHERE name = 'skin'");
        $this->addSql("UPDATE kimai2_user_preferences SET value = 'Europe/Copenhagen' WHERE name = 'timezone'");
    }

    public function down(Schema $schema): void
    {
        // There is no going back!
    }
}
