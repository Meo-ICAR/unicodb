<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Define environment setup.
     * Runs before migrations, so we can register custom SQLite collations here.
     */
    protected function defineEnvironment($app): void
    {
        if ($app['config']->get('database.default') === 'sqlite') {
            // Register utf8mb4_unicode_ci as a no-op collation so that
            // migrations using ->collation('utf8mb4_unicode_ci') work on SQLite.
            $pdo = $app['db']->connection()->getPdo();
            $pdo->sqliteCreateCollation('utf8mb4_unicode_ci', 'strcmp');
        }
    }
}
