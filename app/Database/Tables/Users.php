<?php

namespace App\Database\Tables;

use App\Core\Database;
use App\Database\Migration;

class Users extends Migration
{

    public function up(): Database
    {
        return $this->createTable('users',
            [
                'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
                'username' => 'VARCHAR(100) NOT NULL UNIQUE',
                'email' => 'VARCHAR(100) NOT NULL UNIQUE',
                'password' => 'VARCHAR(255) NOT NULL',
                'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
            ]
        );
    }

    public function down(): Database
    {
        return $this->dropTable('users');
    }

}