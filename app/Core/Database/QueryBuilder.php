<?php

namespace app\Core\Database;

class QueryBuilder
{

    protected static self|null $instance = null;
    protected string $query;
    protected array $conditions = [];
    protected array $with = [];

    public function __construct(
        protected string $table,
    )
    {
        $this->query = "select * from $this->table";
    }

    public static function getInstance(): static
    {
        return static::$instance ??= new static();
    }

    public static function where(string $column, mixed $operator, mixed $value = null): static
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = "=";
        }
        $queryBuilder = self::getInstance();
        $queryBuilder->conditions[] = [$column, $operator, $value];
        return $queryBuilder;
    }

    public static function with(string $relation): static
    {
        $queryBuilder = self::getInstance();
        $queryBuilder->with[] = $relation;
        return $queryBuilder;
    }

    public function toSql(): string
    {
        $baseQuery = $this->query;

//        if (!empty($this->conditions)) {
//            $baseQuery .= " where ";
//            //$this->query .= " where ";
//            foreach ($this->conditions as $index => $condition) {
//                if ($index > 0) {
//                    $baseQuery .= " and ";
//                    //$this->query .= " and ";
//                }
//                $baseQuery .= "{$condition[0]} = :{$condition[0]}";
//                //$this->query .= "{$condition[0]} = :{$condition[0]}";
//            }
//        }

        if (!empty($this->with)) {
            $modelInstance = new static();
            foreach ($this->with as $relation) {
                $related = $modelInstance->$relation();
                $relatedTable = $related->getRelatedTable();
                $foreignColumn = $related->getForeignKey();

                // work out the column names
                $columns = $this->db->execute("SHOW COLUMNS FROM {$relatedTable}")->get();

                $prefixedColumns = array_map(static function ($column) use ($relatedTable, $relation) {
                    return "{$relatedTable}.{$column['Field']} as {$relation}_{$column['Field']}";
                }, $columns);

                $baseQuery = str_replace("select *", "select {$this->table}.*, " . implode(", ", $prefixedColumns), $baseQuery);
                $baseQuery .= " inner join {$relatedTable} on {$this->table}.{$foreignColumn} = {$relatedTable}.id";
                //$this->query = str_replace("select *", "select {$this->table}.*, " . implode(", ", $prefixedColumns), $this->query);
                //$this->query .= " left join {$relatedTable} on {$this->table}.{$foreignColumn} = {$relatedTable}.id";
            }
        }

        if (!empty($this->conditions)) {
            $baseQuery .= " where ";
            //$this->query .= " where ";
            foreach ($this->conditions as $index => $condition) {
                if ($index > 0) {
                    $baseQuery .= " and ";
                    //$this->query .= " and ";
                }
                // if there is a with, we need to prefix the column name
                if (!empty($this->with)) {
                    $baseQuery .= "{$this->table}.{$condition[0]} {$condition[1]} :{$condition[0]}";
                    //$this->query .= "{$this->table}.{$condition[0]} = :{$condition[0]}";
                } else {
                    $baseQuery .= "{$condition[0]} {$condition[1]} :{$condition[0]}";
                    //$this->query .= "{$condition[0]} = :{$condition[0]}";
                }
            }
        }

        return $baseQuery;
    }

    public function getBindings(): array
    {
        $bindings = [];
        foreach ($this->conditions as $condition) {
            $bindings[$condition[0]] = $condition[2];
        }
        return $bindings;
    }

}