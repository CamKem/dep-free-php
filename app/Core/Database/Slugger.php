<?php

namespace app\Core\Database;

class Slugger
{

    public static function slugify(string $string): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    }

    public static function uniqueSlug(string $string, string $model, string $column): string
    {
        $slug = static::slugify($string);
        $count = 1;
        $modelNamespace = 'App\Models\\' . ucfirst($model);
        while (static::slugExists(new $modelNamespace, $column, $slug)) {
            $slug = static::slugify($string) . '-' . $count++;
        }
        return $slug;
    }

    private static function slugExists(Model $model, string $column, string $slug): bool
    {
        $query = $model->query()
            ->select('count(*) as count')
            ->where($column, '=', $slug)
            ->getRaw();
        return $query[0]['count'] > 0;
    }

}