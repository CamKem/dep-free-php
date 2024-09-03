<?php

namespace App\Database\Tables;

use App\Core\Database\Database;
use App\Database\Migration;

class Notes extends Migration
{
    public function up(): Database
    {
        return $this->createTable('Notes',
            [
                'fields' => [
                    'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
                    'body' => 'VARCHAR(255) NOT NULL',
                    'user_id' => 'INT',
                    'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                    'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
                ],
                'foreign_keys' => [
                    'user_id' => 'users(id)'
                ],
                'indexes' => [
                    'user_id'
                ]
            ]
        );
    }

    public function down(): Database
    {
        return $this->dropTable('Notes');
    }
}