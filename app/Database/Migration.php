<?php

// a php class that will be our base class to handle all the migration logic for our database

namespace App\Database;

use App\Core\App;
use app\Core\Database\Database;

class Migration
{

    protected Database $db;

    public function __construct()
    {
        // todo work out for this part of the boundry if we resolve off the app or the container
        $this->db = app::resolve(Database::class);
    }

    public function createTable($table, $fields): Database
    {
        // flatten the array of fields into a string
        $fields = implode(', ', $fields);
        // check if the table exists and if not run the query to create it
        // when it's created we will all add a record to the migrations table, so we can keep track of what has been created
        // first we will check if the migrations table exists and if not we will create it
        $this->addMigrationsTable();

        return $this->db->query("CREATE TABLE IF NOT EXISTS $table ($fields)");
    }

    public function dropTable($table): Database
    {
        return $this->db->query("DROP TABLE IF EXISTS $table");
    }

    public function addColumn($table, $column, $type)
    {
        $this->db->query("ALTER TABLE $table ADD $column $type");
    }

    public function dropColumn($table, $column)
    {
        $this->db->query("ALTER TABLE $table DROP COLUMN $column");
    }

    public function modifyColumn($table, $column, $type)
    {
        $this->db->query("ALTER TABLE $table MODIFY COLUMN $column $type");
    }

    public function addMigrationsTable(): Database
    {
        return $this->createTable('migrations', [
            'id INT AUTO_INCREMENT PRIMARY KEY',
            'migration VARCHAR(255)',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ]);
    }

    public function hasBeenMigrated($migration): bool
    {
        $result = $this->db->query("SELECT * FROM migrations WHERE migration = ?", [$migration]);

        return !empty($result);
    }

    public function markAsMigrated($migration): Database
    {
        return $this->db->query("INSERT INTO migrations (migration) VALUES (?)", [$migration]);
    }

}