<?php

namespace App\Core;

use App\Core\Exceptions\ValidationException;

class Validator
{
    protected array $errors = [];
    protected array $data = [];

    public function validate(array $data, array $rules): self
    {
        foreach ($rules as $field => $fieldRules) {
            if (!is_array($fieldRules)) {
                $fieldRules = [$fieldRules];
            }

            foreach ($fieldRules as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleParams = isset($ruleParts[1]) ? explode(',', $ruleParts[1]) : [];

                if (!method_exists($this, $ruleName)) {
                    throw new ValidationException('Validation rule ' . $ruleName . ' does not exist.');
                }

                $this->{$ruleName}($data, $field, ...$ruleParams);
            }
        }

        $this->data = $data;

        return $this;
    }

    // get method to extract data from the data array, without calling a property directly
    public function get(string $name, $default = null): mixed
    {
        return $this->data[$name] ?? $default;
    }

    /**
     * @param string $table
     * @return string
     */
    public function extractSingularModelNameFromTable(string $table): string
    {
        if (str_ends_with($table, 's')) {
            $table = substr($table, 0, -1);
        } elseif (str_ends_with($table, 'ies')) {
            $table = substr($table, 0, -3) . 'y';
        } elseif (str_ends_with($table, 'es')) {
            $table = substr($table, 0, -2);
        }
        return $table;
    }

    protected function exists(array $data, string $field, string $table): void
    {
        if (!empty($data[$field])) {
            $table = $this->extractSingularModelNameFromTable($table);
            // upper case the model name and check if it exists
            $model = 'App\\Models\\' . ucfirst($table);
            $exists = (new $model)->query()->where($field, $data[$field])->exists();
            if (!$exists) {
                $this->errors[$field][] = 'The ' . $field . ' field does not exist in the ' . $table . ' table.';
            }
        }
    }

    protected function unique(array $data, string $field, string $table): void
    {
        if (!empty($data[$field])) {
            // extract model name from the table, make it singular and lower case
            $table = $this->extractSingularModelNameFromTable($table);
            // upper case the model name
            $model = 'App\\Models\\' . ucfirst($table);

            $exists = (new $model)->query()->where($field, $data[$field])->exists();
            if ($exists) {
                $this->errors[$field][] = 'The ' . $field . ' field already exists in the ' . $table . ' table.';
            }
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    protected function array(array $data, string $field): void
    {
        if (!empty($data[$field]) && !is_array($data[$field])) {
            $this->errors[$field][] = 'The ' . $field . ' field must be an array.';
        }
    }

    protected function required(array $data, string $field): void
    {
        if (empty($data[$field])) {
            $this->errors[$field][] = 'The ' . $field . ' field is required.';
        }
    }

    protected function email(array $data, string $field): void
    {
        if (!empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = 'The ' . $field . ' field must be a valid email address.';
        }
    }

    public function string(array $data, string $field): void
    {
        if (!empty($data[$field]) && !trim($data[$field])) {
            $this->errors[$field][] = 'The ' . $field . ' field must be a string.';
        }
    }

    protected function min(array $data, string $field, int $min): void
    {
        if (!empty($data[$field]) && strlen($data[$field]) < $min) {
            $this->errors[$field][] = 'The ' . $field . ' field must be at least ' . $min . ' characters.';
        }
    }

    protected function max(array $data, string $field, int $max): void
    {
        if (!empty($data[$field]) && strlen($data[$field]) > $max) {
            $this->errors[$field][] = 'The ' . $field . ' field may not be greater than ' . $max . ' characters.';
        }
    }

    public function number(array $data, string $field): void
    {
        if (!empty($data[$field]) && !is_numeric($data[$field])) {
            $this->errors[$field][] = 'The ' . $field . ' field must be a number.';
        }
    }

    protected function integer(array $data, string $field): void
    {
        // check the the value is an integer, if it's a string, check it's a valid integer if it was casted to an integer
        if (!empty($data[$field]) && (!is_numeric($data[$field]) || (string)(int)$data[$field] !== $data[$field])) {
            $this->errors[$field][] = 'The ' . $field . ' field must be an integer.';
        }
    }

    protected function boolean(array &$data, string $field): void
    {
        if (!empty($data[$field]) && !$this->normalizeBoolean($data[$field])) {
            $this->errors[$field][] = 'The ' . $field . ' field must be a boolean.';
        }
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

    public function data(): array
    {
        return $this->data;
    }

    protected function url(array $data, string $field): void
    {
        if (!empty($data[$field]) && filter_var($data[$field], FILTER_VALIDATE_URL) === false) {
            $this->errors[$field][] = 'The ' . $field . ' field must be a valid URL.';
        }
    }

    protected function date(array $data, string $field): void
    {
        if (!empty($data[$field]) && strtotime($data[$field]) === false) {
            $this->errors[$field][] = 'The ' . $field . ' field must be a valid date.';
        }
    }

    protected function match(array $data, string $field, string $fieldToMatch): void
    {
        if (!empty($data[$field]) && $data[$field] !== $data[$fieldToMatch]) {
            $this->errors[$field][] = 'The ' . $field . ' field must match the ' . $fieldToMatch . ' field.';
        }
    }

}
