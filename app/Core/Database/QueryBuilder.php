<?php

namespace app\Core\Database;

use RuntimeException;

class QueryBuilder
{

    protected string $query;
    protected array $conditions = [];
    protected array $orConditions = [];
    protected array $updateValues = [];
    protected array $with = [];

    public function __construct(protected string $table,)
    {
        $this->query = "select * from $this->table";
    }

    public function where(string $column, mixed $operator, mixed $value = null): static
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = "=";
        }
        $this->conditions[] = [$column, $operator, $value];
        return $this;
    }

    public function orWhere(string $column, mixed $operator, mixed $value = null): static
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = "=";
        }
        $this->orConditions[] = [$column, $operator, $value];
        return $this;
    }

    // find method
    public function find(int $id): static
    {
        return $this->where('id', $id);
    }

    public function with(string $relation): static
    {

        $this->with[] = $relation;
        return $this;
    }

    public function toSql(): string
    {
        if (str_starts_with(trim($this->query), 'INSERT')) {
            return $this->query;
        }

        if (!empty($this->with)) {
            $modelInstance = new static();
            foreach ($this->with as $relation) {
                $related = $modelInstance->$relation();
                $relatedTable = $related->getRelatedTable();
                $foreignColumn = $related->getForeignKey();

                $columns = $this->db->execute("SHOW COLUMNS FROM {$relatedTable}")->get();

                $prefixedColumns = array_map(static function ($column) use ($relatedTable, $relation) {
                    return "{$relatedTable}.{$column['Field']} as {$relation}_{$column['Field']}";
                }, $columns);

                $this->query = str_replace("SELECT *", "SELECT {$this->table}.*, " . implode(", ", $prefixedColumns), $this->query);
                $this->query .= " INNER JOIN {$relatedTable} ON {$this->table}.{$foreignColumn} = {$relatedTable}.id";
            }
        }

        if (!empty($this->conditions)) {
            $this->query .= " WHERE ";
            foreach ($this->conditions as $index => $condition) {
                if ($index > 0) {
                    $this->query .= " AND ";
                }
                $this->withCheck($condition);
            }
        }

        if (!empty($this->orConditions)) {
            $this->query .= " OR ";
            foreach ($this->orConditions as $index => $condition) {
                if ($index > 0) {
                    $this->query .= " OR ";
                }
                $this->withCheck($condition);
            }
        }

        return $this->query;
    }

    public function create(array $data): static
    {
        $instance = new static();
        $columns = implode(", ", array_keys($data));
        $placeholders = array_map(fn($key) => ":{$key}", array_keys($data));
        $values = implode(", ", $placeholders);

        $instance->query = "INSERT INTO {$instance->table} ({$columns}) VALUES ({$values})";
        $instance->conditions = array_map(fn($key) => [$key, '=', $data[$key]], array_keys($data));

        // set the attributes of the instance
        foreach ($data as $key => $value) {
            $instance->$key = $value;
        }

        return $instance;
    }

    public function update(array $data): static
    {
        $instance = new static();
        $columns = array_keys($data);
        $setClause = implode(", ", array_map(fn($column) => "{$column} = :{$column}", $columns));
        $instance->query = "UPDATE {$instance->table} SET {$setClause}";
        $instance->conditions = $this->conditions;
        $instance->updateValues = array_map(fn($column) => [$column, '=', $data[$column]], $columns);
        return $instance;
    }

    public function delete(): static
    {
        if (empty($this->conditions) && isset($this->attributes['id'])) {
            $this->conditions[] = ['id', '=', $this->attributes['id']];
        }

        if (empty($this->conditions)) {
            throw new RuntimeException("Delete without conditions is not allowed");
        }
        $this->query = "DELETE FROM {$this->table}";
        return $this;
    }

    public function getBindings(): array
    {
        $bindings = [];
        foreach (array_merge($this->conditions, $this->orConditions, $this->updateValues) as $condition) {
            $bindings[$condition[0]] = $condition[2];
        }
        return $bindings;
    }

    public function withCheck(mixed $condition): void
    {
        if (!empty($this->with)) {
            $this->query .= "{$this->table}.{$condition[0]} {$condition[1]} :{$condition[0]}";
        } else {
            $this->query .= "{$condition[0]} {$condition[1]} :{$condition[0]}";
        }
    }

}