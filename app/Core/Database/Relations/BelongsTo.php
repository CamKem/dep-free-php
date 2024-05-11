<?php

namespace app\Core\Database\Relations;

use app\Core\Database\Model;

class BelongsTo
{
    protected Model $parent;
    protected Model $related;
    protected string $foreignKey;

    public function __construct(Model $parent, Model $related)
    {
        $this->parent = $parent;
        $this->related = $related;
        $this->foreignKey = strtolower(class_basename($related)) . '_id';
    }

    public function getRelatedTable(): string
    {
        return $this->related->getTable();
    }

    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

}