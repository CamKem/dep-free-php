<?php

namespace app\Core\Database;

class QueryBuilder
{

    protected string $query;
    protected array $conditions = [];
    protected array $with = [];

    public function __construct(
        protected string $table,
    )
    {
        $this->query = "select * from $this->table";
    }

    public static function where(string $column, mixed $value): static
    {
        return (new static())->addCondition($column, $value);
       // $this->conditions[] = [$column, $value];
    }

    public static function with(string $relation): static
    {
        $queryBuilder = new static();
        $queryBuilder->with[] = $relation;
        return $queryBuilder;
    }

    public function addCondition(string $column, mixed $value): static
    {
        $this->conditions[] = [$column, $value];
        return $this;
    }

    public function toSql(): string
    {
        if (!empty($this->conditions)) {
            $this->query .= " where ";
            foreach ($this->conditions as $index => $condition) {
                if ($index > 0) {
                    $this->query .= " and ";
                }
                $this->query .= "{$condition[0]} = :{$condition[0]}";
            }
        }

        if (!empty($this->with)) {
            $modelInstance = new static();
            foreach ($this->with as $relation) {
                $related = $modelInstance->$relation();
                $relatedTable = $related->getRelatedTable();
                $foreignColumn = $related->getForeignKey();

                // work out the column names
                $columns = $this->db->execute("SHOW COLUMNS FROM {$relatedTable}")->get();

                $prefixedColumns = array_map(static function ($column) use ($relatedTable) {
                    return "{$relatedTable}.{$column['Field']} as {$relatedTable}_{$column['Field']}";
                }, $columns);

                $this->query = str_replace("select *", "select {$this->table}.*, " . implode(", ", $prefixedColumns), $this->query);
                $this->query .= " left join {$relatedTable} on {$this->table}.{$foreignColumn} = {$relatedTable}.id";
            }
        }

        return $this->query;
    }

    public function getBindings(): array
    {
        return array_combine(array_map(static function ($condition) {
            return $condition[0];
        }, $this->conditions), array_column($this->conditions, 1));
    }

}