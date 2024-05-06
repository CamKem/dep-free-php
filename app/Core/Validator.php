<?php

namespace App\Core;

class Validator
{
    public static function string($value, $min = 1, $max = INF): bool
    {
        $value = trim($value);

        return strlen($value) >= $min && strlen($value) <= $max;
    }

    public static function email(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public static function url(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL);
    }

    public static function number(string $value): bool
    {
        return is_numeric($value);
    }

    public static function date(string $value): bool
    {
        return strtotime($value);
    }

public static function min(string $value, int $min): bool
    {
        return strlen($value) >= $min;
    }

    public static function max(string $value, int $max): bool
    {
        return strlen($value) <= $max;
    }

    public static function match(string $value, string $match): bool
    {
        return $value === $match;
    }

    public static function unique(string $value, string $table, string $column): bool
    {
        $db = app(Database::class);

        $result = $db->query(
            "select * from {$table} where {$column} = :value", compact('value'))
            ->count();

        return $result === 0;
    }

    public static function exists(string $value, string $table, string $column): bool
    {
        $db = app(Database::class);

        $result = $db->query(
            "select * from {$table} where {$column} = :value", compact('value'))
            ->count();

        return $result > 0;
    }

    public static function file(string $value): bool
    {
        return is_uploaded_file($value);
    }

    public static function image(string $value): bool
    {
        return getimagesize($value);
    }

}
