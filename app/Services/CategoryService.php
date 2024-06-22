<?php

namespace App\Services;

use App\Core\ServiceProvider;
use app\Enums\CategoryStatus;
use App\Models\Category;

class CategoryService extends ServiceProvider
{

    public function register(): void
    {
        // No need to store the categories in the container
    }

    public function boot(): void
    {
        if (!session()->has('categories')) {
            $categories = (new Category())
                ->query()
                ->where('status', CategoryStatus::ACTIVE->value)
                ->get();
            session()->set('categories', $categories);
        }
    }
}