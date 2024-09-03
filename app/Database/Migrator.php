<?php

namespace App\Database;

use App\Core\App;
use App\Core\Database\Database;

class Migrator
{
    public function __construct(private Database $db)
    {
        $this->db = app(Database::class);
    }

    public function migrate(): void
    {
        foreach (glob(include_path('app/Database/Tables/*.php')) as $migrationFile) {
            $migrationClass = 'App\\Database\\Tables\\' . basename($migrationFile, '.php');

            if ($migrationClass === 'App\\Database\\Tables\\Migration') {
                continue;
            }

            $migration = new $migrationClass();

            // Run the migration
            $migration->up();

            // Record the migration
            $this->recordMigration($migrationClass);
        }
    }

    protected function recordMigration(string $migrationClass): void
    {
        $this->db->query("INSERT INTO migrations (migration) VALUES (?)", [$migrationClass]);
    }
}