<?php

namespace App\Services;

use App\Core\Authentication\Auth;
use App\Core\FileSystem\Storage;
use App\Core\ServiceProvider;

class StorageService extends ServiceProvider
{
    public function register(): void
    {
        // Register Storage service
        $this->app->singleton(Storage::class);
    }

    public function boot(): void
    {
        // Boot the service
    }
}