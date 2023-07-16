<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230301182945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema
            ->getTable('articles');
        $table
            ->addColumn('created_at', 'datetime')
            ->setDefault(NULL)
            ->setNotnull(false);
        $table
            ->addColumn('updated_at', 'datetime')
            ->setDefault(NULL)
            ->setNotnull(false);
    }

    public function down(Schema $schema): void
    {
        $table = $schema
            ->getTable('articles');
        $table
            ->dropColumn('created_at');
        $table
            ->dropColumn('updated_at');
    }
}
