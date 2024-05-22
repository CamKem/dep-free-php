<?php

namespace app\Core\Database\Relations;

use app\Core\Database\Model;
use app\Core\Database\QueryBuilder;

class HasManyThrough
{

    public function __construct(
        protected Model $parent,
        protected Model $related,
        protected string $pivotTable,
        protected string $foreignKey,
        protected string $relatedKey)
    {}

    public function getRelatedTable(): string
    {
        return $this->related->getTable();
    }

    public function getPivotTable(): string
    {
        return $this->pivotTable;
    }

    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    public function getRelatedKey(): string
    {
        return $this->relatedKey;
    }

    public function query(): QueryBuilder
    {
        return $this->related->query()->where('id', 'IN', function ($query) {
            $query->select($this->getRelatedKey())->from($this->getPivotTable())->where($this->getForeignKey(), $this->parent->id);
        });
    }

    // TODO: get this working

    // implement __toString() to return the SQL query
    public function __toString(): string
    {
        return "SELECT * FROM {$this->getRelatedTable()} WHERE id IN (SELECT {$this->getRelatedKey()} FROM {$this->getPivotTable()} WHERE {$this->getForeignKey()} = {$this->parent->id})";
    }

}