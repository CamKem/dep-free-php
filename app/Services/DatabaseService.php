<?php

namespace App\Services;

use app\Core\Database\Database;
use App\Core\ServiceProvider;
use App\Database\Migrator;
use Override;

class DatabaseService extends ServiceProvider
{

    #[Override]
    public function register(): void
    {
        $this->app->singleton(Database::class);

        $this->app->singleton(Migrator::class);
    }

    /**
     * Connect to the database and keep the connection alive
     * @return void
     * @uses Database::connect()
     */
    #[Override]
    public function boot(): void
    {
        $this->app->resolve(Database::class)->connect();
    }

    public function unBoot(): void
    {
        $this->app->resolve(Database::class)->disconnect();
    }

}