<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230226133827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создает таблицу articles в бд';
    }

    public function up(Schema $schema): void
    {
        $table = $schema
            ->createTable('articles');

        $table
            ->addColumn('id', 'integer')
            ->setAutoincrement(true)
            ->setNotnull(true);

        $table
            ->addColumn('title', 'string')
            ->setLength(100)
            ->setNotnull(true);

        $table
            ->addColumn('slug', 'string')
            ->setLength(100)
            ->setNotnull(true);

        $table
            ->addColumn('description', 'string')
            ->setLength(100)
            ->setNotnull(false);

        $table
            ->addColumn('body', 'string')
            ->setLength(255)
            ->setNotnull(true);

        $table
            ->addColumn('keywords', 'string')
            ->setNotnull(false);

        $table
            ->addColumn('vote_count', 'integer')
            ->setNotnull(false)
            ->setDefault(0);

        $table
            ->addColumn('image_filename', 'string')
            ->setLength(100)
            ->setNotnull(false);

        $table
            ->addColumn('published_at', 'datetime')
            ->setNotnull(true);

        $table->addUniqueConstraint(['slug']);
        $table->setPrimaryKey(['id']);

    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('articles');
    }
}
