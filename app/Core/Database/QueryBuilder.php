<?php

namespace App\Core\Database;

use App\Core\Collecting\ModelCollection;
use App\Core\Collecting\Paginator;
use App\Core\Database\Relations\BelongsTo;
use App\Core\Database\Relations\HasMany;
use App\Core\Database\Relations\HasManyThrough;
use RuntimeException;

class QueryBuilder
{

    public Database $db;
    protected string $query;
    protected string $table;
    protected array $select = ['*'];
    protected array $raw = [];
    protected array $conditions = [];
    protected array $orConditions = [];
    protected array $orderBy = [];
    protected array $groupBy = [];
    protected array $limit = [];
    protected array $offset = [];
    protected array $joins = [];
    protected string $verb = 'SELECT';
    protected array $updateValues = [];
    protected array $with = [];
    protected array $withCount = [];
    protected bool $withRelatedColumns = true;
    protected ?Relation $relation = null;

    public function __construct(protected Model $model)
    {
        $this->table = $model->getTable();
        $this->db = app(Database::class);
    }

    public function setRelation(Relation $relation): static
    {
        $this->relation = $relation;
        return $this;
    }

    public function from(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second, string $type = 'LEFT'): static
    {
        $this->joins[] = "{$type} JOIN {$table} ON {$first} {$operator} {$second}";
        return $this;
    }

    public function select(string|array $columns): static
    {
        // If the SELECT clause has already been modified, append the new columns
        if ($this->select !== ['*']) {
            if (is_array($columns)) {
                $this->select = array_merge($this->select, $columns);
            } else {
                $this->select[] = $columns;
            }
            // If the SELECT clause has not been modified, set the new columns
        } else if (is_array($columns)) {
            $this->select = $columns;
            // else set the columns as an array
        } else {
            $this->select = [$columns];
        }

        return $this;
    }

    public function groupBy(string|array $columns): static
    {
        if (is_array($columns)) {
            $this->groupBy = $columns;
        } else {
            $this->groupBy = [$columns];
        }
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $column = $this->checkPrefixRelation($column);
        $this->orderBy = [$column, $direction];
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit[] = $limit;
        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offset[] = $offset;
        return $this;
    }

    public function whereIn(string $column, array $values): static
    {
        if (empty($values)) {
            // No results should be returned if $values are empty
            $this->conditions[] = [$column, 'IN', NULL, "{$column}_in_0"];
            return $this;
        }

        // Ensure the column name is valid
        $column = $this->checkPrefixRelation($column);

        // Generate unique placeholders
        foreach ($values as $index => $value) {
            $placeholder = "{$column}_in_{$index}";
            $this->conditions[] = [$column, "IN", $value, $placeholder];
        }

        return $this;
    }

    public function where(string $column, mixed $operator, mixed $value = null): static
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = "=";
        }

        $column = $this->checkPrefixRelation($column);

        $this->conditions[] = [$column, $operator, $value];
        return $this;
    }

    public function orWhere(string $column, mixed $operator, mixed $value = null): static
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = "=";
        }

        $column = $this->checkPrefixRelation($column);

        $this->orConditions[] = [$column, $operator, $value];
        return $this;
    }

    // whereHas method, checks relationship existence
    // TODO: we can make this better so we don't have to use the pivot table as a prefix in the
    //  sub query, like in the UsersController index method.
    public function whereHas(string $relation, callable $callback): static
    {
        // check the relation exists on the model
        if (!method_exists(new $this->model(), $relation)) {
            throw new RuntimeException("Method {$relation} does not exist on the model");
        }

        // call the relation to get the object
        $relationInst = $this->model->{$relation}();

        // with the relation set, we can now get the related model
        $this->with($relation);

        // call the callback function, pass the query builder instance
        $query = $callback($relationInst->query(where: false));


        // then merge the conditions from the callback with the current conditions
        $this->conditions = array_merge(
            $this->conditions,
            $query->conditions,
        );

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

    public function withCount(string $relation): static
    {
        if (!method_exists(new $this->model(), $relation)) {
            throw new RuntimeException("Method {$relation} does not exist on the model");
        }

        $this->withCount[] = $relation;
        return $this;
    }

    public function getRaw(): array
    {
        return $this->db->execute($this->toSql(), $this->getBindings())->get();
    }

    public function commonRelated(Relation $relation): void
    {
        $columns = $this->db->execute("SHOW COLUMNS FROM {$relation->getRelatedTable()}")->get();
        $prefixedColumns = array_map(static function ($column) use ($relation) {
            return "{$relation->getRelatedTable()}.{$column['Field']} as {$relation->getRelationName()}_{$column['Field']}";
        }, $columns);
        $this->select($prefixedColumns);
    }

    public function toSql(): string
    {
        // clear the query string
        $this->query = '';

        if (str_starts_with(trim($this->query), 'INSERT')) {
            return $this->query;
        }

        if (!empty($this->with)) {
            $modelInstance = new $this->model();
            foreach ($this->with as $relation) {
                if (method_exists($modelInstance, $relation)) {
                    $relation = $modelInstance->{$relation}();

                    $parentTable = $relation->getParentTable();
                    $relatedTable = $relation->getRelatedTable();
                    $foreignColumn = $relation->getForeignKey();
                    $relatedColumn = $relation->getRelatedKey();

                    if ($relation instanceof HasManyThrough) {
                        $pivotTable = $relation->getPivotTable();

                        if ($this->withRelatedColumns) {

                            $this->select("{$parentTable}.*");
                            $this->commonRelated($relation);

                            if ($relation->withPivot) {
                                $pivotColumns = $this->db->execute("SHOW COLUMNS FROM {$pivotTable}")->get();
                                $prefixedPivotColumns = array_map(static function ($column) use ($pivotTable) {
                                    return "{$pivotTable}.{$column['Field']} as pivot_{$column['Field']}";
                                }, $pivotColumns);
                                $this->select($prefixedPivotColumns);
                            }
                        }

                        $this->join($pivotTable, "{$pivotTable}.{$foreignColumn}", "=", "{$parentTable}.id");
                        $this->join($relatedTable, "{$relatedTable}.id", "=", "{$pivotTable}.{$relatedColumn}");
                    }
                    if ($relation instanceof BelongsTo) {
                        if ($this->withRelatedColumns) {
                            $this->select("{$this->table}.*");
                            $this->commonRelated($relation);
                        }
                        $this->join($relatedTable, "{$this->table}.{$foreignColumn}", '=', "{$relatedTable}.id");
                    }
                    if ($relation instanceof HasMany) {
                        if ($this->withRelatedColumns) {
                            $this->select("{$this->table}.*");
                            $this->commonRelated($relation);
                        }
                        $this->join($relatedTable, "{$relatedTable}.{$foreignColumn}", '=', "{$this->table}.id");

                    }
                }
            }
        }

        if (!empty($this->withCount)) {
            $modelInstance = new $this->model();
            foreach ($this->withCount as $relation) {
                if (method_exists($modelInstance, $relation)) {
                    $relation = $modelInstance->{$relation}();
                    $relatedTable = $relation->getRelatedTable();
                    $foreignColumn = $relation->getForeignKey();
                    $relatedColumn = $relation->getRelatedKey();
                    $relationName = $relation->getRelationName();

                    $this->select("{$this->table}.*");
                    if ($relation instanceof HasMany || $relation instanceof BelongsTo) {
                        $this->select("COUNT(DISTINCT {$relatedTable}.id) AS {$relationName}_count");
                        $this->join($relatedTable, "{$relatedTable}.{$foreignColumn}", '=', "{$this->table}.id", 'LEFT');
                    } elseif ($relation instanceof HasManyThrough) {
                        $pivotTable = $relation->getPivotTable();
                        $this->select[] = "COUNT(DISTINCT {$relatedTable}.id) AS {$relationName}_count";
                        $this->join($pivotTable, "{$pivotTable}.{$foreignColumn}", '=', "{$this->table}.id", 'LEFT');
                        $this->join($relatedTable, "{$relatedTable}.id", '=', "{$pivotTable}.{$relatedColumn}", 'LEFT');
                    } else {
                        $this->select[] = "COUNT(DISTINCT {$relatedTable}.id) AS {$relationName}_count";
                        $this->join($relatedTable, "{$relatedTable}.{$foreignColumn}", '=', "{$this->table}.id", 'LEFT');
                    }
                    $this->groupBy("{$this->table}.id");
                }
            }
        }

        if ($this->verb === 'DELETE') {
            $this->query = "DELETE FROM {$this->table}";
        } elseif ($this->verb === 'INSERT') {
            $columns = array_map(fn($condition) => $condition[0], $this->conditions);
            $this->query = "INSERT INTO {$this->table} (" . implode(", ", $columns) . ") VALUES (:" . implode(", :", $columns) . ")";
            // early return because we don't need to do anything else
            return $this->query;
        } elseif ($this->verb === 'UPDATE') {
            // get the columns from the updateValues
            $columns = array_map(fn($condition) => $condition[0], $this->updateValues);
            $setClause = implode(", ", array_map(fn($column) => "{$column} = :{$column}", $columns));
            $this->query = "UPDATE {$this->table} SET {$setClause}";
        } elseif ($this->verb === 'SELECT') {
            // we need to handle the selectRaw here, it will be stored in $raw['select'] array
            // we will loop through the array and add the raw select to the query
            if (!empty($this->raw)) {
                foreach ($this->raw as $key => $value) {
                    if ($key === 'select') {
                        foreach ($value as $raw) {
                            $this->select[] = $raw;
                        }
                    }
                }
            }
            $this->query = "SELECT " . implode(", ", array_unique($this->select)) . " FROM {$this->table}";
        }

        // handle from clause
        if (!empty($this->from)) {
            $this->query = str_replace("FROM {$this->table}", "FROM " . implode(", ", $this->from), $this->query);
        }

        // handle join clause
        if (!empty($this->joins)) {
            // ensure they joins are unique
            $this->joins = array_unique($this->joins);
            foreach ($this->joins as $join) {
                // search the query for the join needle and ensure it's not already in the query
                if (!str_contains($this->query, $join)) {
                    $this->query .= " {$join}";
                }
            }
        }

        // handle where clause
        if (!empty($this->conditions) && !str_contains($this->query, 'WHERE')) {

            // if conditions contain the IN identifier then we need to handle it differently like so :$column IN ($values)
            $inConditions = array_values(
                array_filter($this->conditions, static fn($condition) => $condition[1] === 'IN')
            );

            // filter out the inConditions from the conditions array, and whats left is the remaining conditions
            $remainingConditions = array_values(
                array_filter($this->conditions, static fn($condition) => $condition[1] !== 'IN')
            );

            if (!empty($inConditions)) {
                // column is the first index of the first inCondition
                // we can't assume that it will be key 0 because of filtering.
                // we will get the first index of the inConditions array
                $column = array_values($inConditions)[0][0];
                $this->query .= " WHERE {$this->table}.{$column} IN (";
                foreach ($inConditions as $index => $condition) {
                    // get condition[2] and remove
                    // if it's not the last index then add a comma
                    $this->query .= ":{$condition[3]}";
                    if ($index < count($inConditions) - 1) {
                        $this->query .= ", ";
                    }
                }
                $this->query .= ")";
            }
            if (!empty($remainingConditions)) {
                $this->query .= " WHERE ";
                foreach ($remainingConditions as $index => $condition) {
                    if ($index > 0) {
                        $this->query .= " AND ";
                    }
                    $this->withCheck($condition);
                }
            }
        }

        // handle orWhere clause
        if (!empty($this->orConditions) && !str_contains($this->query, 'OR')) {
            $this->query .= " OR ";
            foreach ($this->orConditions as $index => $condition) {
                if ($index > 0) {
                    $this->query .= " OR ";
                }
                $this->withCheck($condition);
            }
        }

        // handle the raw where clause
        if (!empty($this->raw) && !str_contains($this->query, 'WHERE')) {
            foreach ($this->raw as $key => $value) {
                if ($key === 'where') {
                    // TODO: ensure this works with multiple raw where clauses
                    //foreach ($value as $raw) {
                    //  $this->query .= " WHERE {$raw}";
                    //}
                    $this->query .= " WHERE ";
                    foreach ($value as $index => $raw) {
                        if ($index > 0) {
                            $this->query .= " AND ";
                        }
                        $this->query .= $raw;
                    }
                }
                // handle the orWhere raw clause
                if ($key === 'orWhere') {
                    $this->query .= " OR ";
                    foreach ($value as $index => $raw) {
                        if ($index > 0) {
                            $this->query .= " OR ";
                        }
                        $this->query .= $raw;
                    }
                }
            }
        }

        // handle a group by clause
        if (!empty($this->groupBy) && !str_contains($this->query, 'GROUP BY')) {
            $this->query .= " GROUP BY " . implode(", ", $this->groupBy);
        }

        // handle order by clause
        if (!empty($this->orderBy) && !str_contains($this->query, 'ORDER BY')) {
            $this->query .= " ORDER BY ";
            $this->query .= "{$this->orderBy[0]} {$this->orderBy[1]}";
        }

        // handle limit clause
        if (!empty($this->limit) && !str_contains($this->query, 'LIMIT')) {
            $this->query .= " LIMIT {$this->limit[0]}";
        }

        // handle offset clause
        if (!empty($this->offset) && !str_contains($this->query, 'OFFSET')) {
            $this->query .= " OFFSET {$this->offset[0]}";
        }

        return $this->query;
    }

    public function create(array $data): static
    {
        $this->verb = 'INSERT';
        $this->conditions = array_map(static fn($key) => [$key, '=', $data[$key]], array_keys($data));
        return $this;
    }

    public function update(array $data): static
    {
        $this->verb = 'UPDATE';
        $this->getConditionFromPrimaryKey();
        $columns = array_keys($data);
        $this->updateValues = array_map(fn($column) => [$column, '=', $data[$column]], $columns);
        return $this;
    }

    public function checkPrefixRelation(string $column): string
    {
        // Check if the column name is already prefixed with the table name
        // NOTE: this was HasManyThrough only, I checked to Relation
        //  ensure that this is adding the prefix to where conditions when there is a relation
        //  be mindful that the withCheck method is also adding the prefix, so we need to add it in 1 place.
        if ($this->relation instanceof Relation && !str_contains($column, '.') && !str_starts_with($column, "{$this->table}.")) {
            $column = "{$this->relation->getRelatedTable()}.{$column}";
        }
        return $column;
    }

    public function clearConditions(): void
    {
        $this->joins = [];
        $this->select = ["*"];
        $this->conditions = [];
        $this->orConditions = [];
        $this->orderBy = [];
        $this->limit = [];
        $this->offset = [];
        // clear the raw array
        $this->raw = [];
    }

    // the whereRaw method should allow the user to pass in a raw SQL string,
    // which will be appended to the query string.
    public function whereRaw(string $string): static
    {
        $this->raw['where'][] = $string;
        return $this;
    }

    public function orWhereRaw(string $string)
    {
        $this->raw['orWhere'][] = $string;
        return $this;
    }

    public function selectRaw(string $string): static
    {
        $this->raw['select'][] = $string;
        return $this;
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
        $this->verb = 'DELETE';
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
            // handle the whereIn clause
            if (isset($condition[3])) {
                // the condition will be like this ['column', 'IN', 'value', 'placeholder']
                // we will use this to add the placeholder as the key and the value as the value
                $bindings[$condition[3]] = $condition[2];
            } else {
                // handle the normal conditions
                $bindings[$this->replacePeriod($condition[0])] = $condition[2];
            }
        }
        return $bindings;
    }

    public function replacePeriod(mixed $binding): mixed
    {
        if (str_contains($binding, '.')) {
            $binding = str_replace('.', '_', $binding);
        }
        return $binding;
    }

    public function withCheck(mixed $condition): void
    {
        // make sure there is no period needle already in the column
        if ((!empty($this->with) || !empty($this->withCount)) && !str_contains($condition[0], '.')) {
            $this->query .= "{$this->table}.";
        }
        $this->query .= "{$condition[0]} {$condition[1]} :{$this->replacePeriod($condition[0])}";
    }


    public function get(): ModelCollection
    {
        return $this->model->hydrate(
            $this->db->execute(
                $this->toSql(),
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

    public function paginate(int $perPage = 10): Paginator
    {
        // Get the current page, default to 1 if not set
        $currentPage = (int)request()->get('page', 1);
        // Ensure the current page is at least 1
        $currentPage = max(1, $currentPage);

        $currentSelects = $this->select;

        $this->select = [];

        $this->withRelatedColumns = false;
        // Get the total number of rows
        $this->select("DISTINCT {$this->table}.id");

        // if there is an orderBy present we need to prefix the column with the table name
        if (!empty($this->orderBy)) {
            $this->orderBy = ["{$this->table}.{$this->orderBy[0]}", $this->orderBy[1]];
            $this->select($this->orderBy[0]);
        }

        $totalRows = $this->db->execute(
            $this->toSql(),
            $this->getBindings()
        )->count();

        // Calculate the last page number
        $lastPage = (int)ceil($totalRows / $perPage);
        // Calculate the offset for the SQL query
        $offset = ($currentPage - 1) * $perPage;

        // Ensure the offset is not out of bounds
        if ($offset >= $totalRows) {
            $offset = max(0, ($lastPage - 1) * $perPage);
            $currentPage = $lastPage;
        }

        // Set the limit and offset for the query
        $this->limit($perPage);
        $this->offset($offset);

        // Get distinct category id's to then use in the main query
        $ids = $this->db->execute(
            $this->toSql(),
            $this->getBindings()
        )->get();

        // clear limit & offset & select
        $this->limit = [];
        $this->offset = [];

        // replace the select
        $this->select = $currentSelects;

        // set the withRelatedColumns to true
        $this->withRelatedColumns = true;

        // we need the id's to be a flat array, like [1, 2, 3, 4, 5]
        $ids = array_map(static fn($id) => $id['id'], $ids);

        // remove any existing where clauses
        $this->conditions = [];
        $this->orConditions = [];

        // add the whereIn clause to the query
        $this->whereIn("id", $ids);

        // Execute the query and hydrate the items
        $items = $this->model->hydrate(
            $this->db->execute(
                $this->toSql(),
                $this->getBindings()
            )->get()
        );

        // Create and return the paginator object
        return new Paginator($items, $currentPage, $lastPage);
    }

}