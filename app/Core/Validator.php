<?php

namespace App\Core;

use App\Core\Exceptions\ValidationException;

class Validator
{
    protected array $errors = [];

    public function validate(array $data, array $rules): array
    {
        foreach ($rules as $field => $fieldRules) {
            if (!is_array($fieldRules)) {
                $fieldRules = [$fieldRules];
            }

            foreach ($fieldRules as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleParams = isset($ruleParts[1]) ? explode(',', $ruleParts[1]) : [];

                $this->{$ruleName}($data, $field, ...$ruleParams);
            }
        }

        if (!empty($this->errors)) {
            // TODO: work out a way to return the errors instead of throwing an exception
            //return $this->errors;
            throw new ValidationException($this->errors);
        }

        return $data;
    }

    protected function required(array $data, string $field)
    {
        if (empty($data[$field])) {
            $this->errors[$field][] = 'The ' . $field . ' field is required.';
        }
    }

    protected function email(array $data, string $field)
    {
        if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = 'The ' . $field . ' field must be a valid email address.';
        }
    }

    public function string(array $data, string $field): void
    {
        if (!trim($data[$field])) {
            $this->errors[$field][] = 'The ' . $field . ' field must be a string.';
        }
    }

    protected function min(array $data, string $field, int $min)
    {
        if (strlen($data[$field]) < $min) {
            $this->errors[$field][] = 'The ' . $field . ' field must be at least ' . $min . ' characters.';
        }
    }

    protected function max(array $data, string $field, int $max)
    {
        if (strlen($data[$field]) > $max) {
            $this->errors[$field][] = 'The ' . $field . ' field may not be greater than ' . $max . ' characters.';
        }
    }

    protected function boolean(array &$data, string $field): void
    {
        $data[$field] = $this->normalizeBoolean($data[$field]);
    }

    public function normalizeBoolean($value): bool
    {
        if (is_bool($value) || is_numeric($value)) {
            return (bool) $value;
        }

        if ($value === 'on') {
            return true;
        }

        return false;
    }

    // TODO: fix the validation rules errors below here:

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

    public static function match(string $value, string $match): bool
    {
        return $value === $match;
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
