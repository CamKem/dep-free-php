<?php

namespace app\Core\Database\Relations;

use App\Core\Collecting\ModelCollection;
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

    public function getRelated(): ModelCollection
    {
        // get the result of the related model
        return $this->related::where(
            'id',
            $this->parent->{$this->foreignKey}
        )->all();
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