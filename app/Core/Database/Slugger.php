<?php

namespace App\Core\Database;

class Slugger
{

    public static function slugify(string $string): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    }

    public static function uniqueSlug(string $string, string $model, string $column): string
    {
        $slug = static::slugify($string);
        $last = static::lastSuffix(new $model, $column, $slug);
        $slug .= ($last ? '-' . ($last + 1) : '');
        return $slug;
    }

    private static function lastSuffix(Model $model, string $column, string $slug): int
    {
        return (int) $model->query()
                ->addRaw("
                WITH slug_suffix AS (
                    SELECT MAX(CAST(SUBSTRING_INDEX({$column}, '-', -1) AS UNSIGNED)) as max_suffix
                    FROM {$model->getTable()}
                    WHERE {$column} LIKE '{$slug}-%'
                )
            ")
            ->selectRaw('max_suffix')
            ->from('slug_suffix')
            ->first()
            ->max_suffix;
    }

}