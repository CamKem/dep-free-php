<?php

namespace App\Services;

use App\Core\ServiceProvider;
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
                ->get();
            session()->set('categories', $categories->toArray());
        }
    }
}