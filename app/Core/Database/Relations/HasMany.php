<?php

namespace app\Core\Database\Relations;

use App\Core\Collecting\ModelCollection;
use app\Core\Database\Model;

class HasMany
{
    protected Model $parent;
    protected Model $related;
    protected string $foreignKey;

    public function __construct(Model $parent, Model $related)
    {
        $this->parent = $parent;
        $this->related = $related;
        $this->foreignKey = strtolower(class_basename($parent)) . '_id';
    }

    public function getRelatedTable(): string
    {
        return $this->related->getTable();
    }

    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    public function get(): ModelCollection
    {
        return $this->related->where($this->foreignKey, $this->parent->id)->get();
    }

}