<?php

namespace app\Core\Database;

use App\Core\Collecting\ModelCollection;
use app\Core\Database\Relations\BelongsTo;
use app\Core\Database\Relations\HasMany;
use app\Core\Database\Relations\HasManyThrough;
use RuntimeException;

class QueryBuilder
{

    public Database $db;
    protected string $query;
    protected string $table;
    protected string $select = "*";
    protected array $conditions = [];
    protected array $orConditions = [];
    protected array $updateValues = [];
    protected array $with = [];

    public function __construct(protected Model $model)
    {
        $this->table = $model->getTable();
        $this->db = app(Database::class);
        $this->query = "SELECT {$this->select} FROM {$this->table}";
    }

    public function setRelation(Relation $relation): static
    {
        $this->relation = $relation;
        return $this;
    }

    public function from(string $table): static
    {
        $this->query = str_replace("FROM {$this->table}", "FROM {$table}", $this->query);
        $this->table = $table;
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second): static
    {
        $this->query .= " INNER JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    public function select(string ...$columns): static
    {
        $this->select = implode(", ", $columns);
        $this->query = str_replace("SELECT *", "SELECT {$this->select}", $this->query);
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $this->orderBy[] = [$column, $direction];
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit[] = $limit;
        return $this;
    }

    public function whereIn(string $column, array $values): static
    {
        if (empty($values)) {
            $this->conditions[] = [$column, '=', 'NULL'];
            // $this->conditions[] = [$column, "IN", "(?)", ['NULL']];
            return $this;
        }
        // Create a placeholder for each value
        $placeholders = implode(", ", array_fill(0, count($values), "?"));

        // Add the condition with the placeholders
        $this->conditions[] = [$column, "IN", "({$placeholders})", $values];
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
        if (!method_exists(new $this->model(), $relation)) {
            throw new RuntimeException("Method {$relation} does not exist on the model");
        }
        $this->with[] = $relation;
        return $this;
    }

    public function toSql(): string
    {
        if (str_starts_with(trim($this->query), 'INSERT')) {
            return $this->query;
        }

        if (!empty($this->with)) {
            $modelInstance = new $this->model();
            foreach ($this->with as $relation) {
                if (method_exists($modelInstance, $relation)) {
                    $relation = $modelInstance->{$relation}();
                    if ($relation instanceof HasManyThrough) {
                        $relatedTable = $relation->getRelatedTable();
                        $pivotTable = $relation->getPivotTable();
                        $foreignColumn = $relation->getForeignKey();
                        $relatedColumn = $relation->getRelatedKey();

                        // get the columns of the related table
                        $columns = $this->db->execute("SHOW COLUMNS FROM {$relatedTable}")->get();

                        $prefixedColumns = array_map(static function ($column) use ($relatedTable, $relation) {
                            return "{$relatedTable}.{$column['Field']} as {$relation}_{$column['Field']}";
                        }, $columns);

                        $this->query = str_replace("SELECT *", "SELECT {$this->table}.*, " . implode(", ", $prefixedColumns), $this->query);
                        $this->query .= " INNER JOIN {$pivotTable} ON {$this->table}.id = {$pivotTable}.{$foreignColumn}";
                        $this->query .= " INNER JOIN {$relatedTable} ON {$pivotTable}.{$relatedColumn} = {$relatedTable}.id";
                    }
                    if ($relation instanceof BelongsTo) {
                        [$relatedTable, $foreignColumn] = $this->commonRelated($relation);
                        $this->query .= " INNER JOIN {$relatedTable} ON {$this->table}.{$foreignColumn} = {$relatedTable}.id";
                    }
                    if ($relation instanceof HasMany) {
                        [$relatedTable, $foreignColumn] = $this->commonRelated($relation);
                        $this->query .= " INNER JOIN {$relatedTable} ON {$relatedTable}.{$foreignColumn} = {$this->table}.id";
                    }
                }
            }
        }

        // handle where clause
        if (!empty($this->conditions)) {
            $this->query .= " WHERE ";
            foreach ($this->conditions as $index => $condition) {
                if ($index > 0) {
                    $this->query .= " AND ";
                }
                $this->withCheck($condition);
            }
        }

        // handle orWhere clause
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
        $columns = implode(", ", array_keys($data));
        $placeholders = array_map(fn($key) => ":{$key}", array_keys($data));
        $values = implode(", ", $placeholders);

        $this->query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$values})";
        $this->conditions = array_map(static fn($key) => [$key, '=', $data[$key]], array_keys($data));

        return $this;
    }

    public function update(array $data): static
    {
        $this->getConditionFromPrimaryKey();
        $columns = array_keys($data);
        $setClause = implode(", ", array_map(fn($column) => "{$column} = :{$column}", $columns));
        $this->query = "UPDATE {$this->table} SET {$setClause}";
        $this->updateValues = array_map(fn($column) => [$column, '=', $data[$column]], $columns);
        return $this;
    }

    public function commonRelated(BelongsTo|HasMany $relation): array
    {
        $relatedTable = $relation->getRelatedTable();
        $foreignColumn = $relation->getForeignKey();
        $columns = $this->db->execute("SHOW COLUMNS FROM {$relatedTable}")->get();

        $prefixedColumns = array_map(static function ($column) use ($relatedTable, $relation) {
            return "{$relatedTable}.{$column['Field']} as {$relation->getRelationName()}_{$column['Field']}";
        }, $columns);

        $this->query = str_replace("SELECT *", "SELECT {$this->table}.*, " . implode(", ", $prefixedColumns), $this->query);
        return array($relatedTable, $foreignColumn);
    }

    protected function getConditionsFromAttributes(): void
    {
        foreach ($this->model->getAttributes() as $attribute => $value) {
            $this->conditions[] = [$attribute, '=', $value];
        }
    }

    protected function getConditionFromPrimaryKey(): void
    {
        if (empty($this->conditions)) {
            $primaryKey = $this->model->getPrimaryKey();
            $this->conditions[] = [$primaryKey, '=', $this->model->$primaryKey];
        }
    }

    public function delete(): static
    {
        $this->getConditionFromPrimaryKey();
        $this->query = "DELETE FROM {$this->table}";
        return $this;
    }

    public function save(): bool
    {
        return $this->db->execute(
                $this->toSql(),
                $this->getBindings(),
            )->count() >= 1;
    }

    public function getBindings(): array
    {
        $bindings = [];
        $conditions = array_merge($this->conditions, $this->orConditions, $this->updateValues) ?? [];
        foreach ($conditions as $condition) {
            if (isset($condition[3]) && is_array($condition[3])) {
                foreach ($condition[3] as $key => $value) {
                    $bindings[$key+1] = $value;
                }
                return $bindings;
            }
            $bindings[$condition[0]] = $condition[2];
        }
        return $bindings;
    }

    public function withCheck(mixed $condition): void
    {
        if (!empty($this->with)) {
            $this->query .= "{$this->table}.";
        }
        $this->positionalCheck($condition);
    }

    public function positionalCheck(mixed $condition): void
    {
        if ($condition[1] === "IN" && str_contains($condition[2], '?')) {
            $this->query .= "{$condition[0]} {$condition[1]} {$condition[2]}";
        } else {
            $this->query .= "{$condition[0]} {$condition[1]} :{$condition[0]}";
        }
    }

    public function all(): ModelCollection
    {
        return $this->model->hydrate(
            $this->db->execute(
                $this->toSql(),
                $this->getBindings(),
            )->get()
        );
    }

    public function get(): ModelCollection
    {
        $sql = $this->toSql();
        logger("Query: {$this->db->raw($sql, $this->getBindings())->queryString}");
        return $this->model->hydrate(
            $this->db->execute(
                $sql,
                $this->getBindings()
            )->get()
        );
    }

    public function first()
    {
        $results = $this->db->execute(
            $this->toSql(),
            $this->getBindings(),
        )->get();
        if (empty($results)) {
            return null;
        }
        return $this->model->hydrate($results)->first();
    }

    public function exists(): bool
    {
        return ($this->db->execute(
                $this->toSql(),
                $this->getBindings(),
            )->count()) > 0;
    }

}