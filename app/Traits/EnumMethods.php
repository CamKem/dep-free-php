<?php

namespace App\Traits;

trait EnumMethods
{
    public static function toValues(): array
    {
        // use the cases() method to return the values of the enum in an array dynamically
        $values = [];
        foreach (self::cases() as $case) {
            $values[] = $case->value;
        }
        return $values;
    }

}