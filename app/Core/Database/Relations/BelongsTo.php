<?php

namespace app\Core\Database\Relations;

use app\Core\Database\Model;
use app\Core\Database\QueryBuilder;
use app\Core\Database\Relation;
use Override;

class BelongsTo extends Relation
{
    public function __construct(
        protected Model $parent,
        protected Model $related
    )
    {
    }

    #[Override]
    public function getParentTable(): string
    {
        return $this->parent->getTable();
    }

    #[Override]
    public function getRelatedKey(): string
    {
        return $this->related->getPrimaryKey();
    }

    #[Override]
    public function getRelatedTable(): string
    {
        return $this->related->getTable();
    }

    #[Override]
    public function getForeignKey(): string
    {
        return strtolower(class_basename($this->related)) . '_id';
    }

    // TODO: check that this is working
    #[Override]
    public function query(): QueryBuilder
    {
        return $this->related->query()
            ->setRelation($this)
            ->where(
                'id',
                $this->parent->{$this->getRelatedKey()}
            );
    }

    public function getRelationName(): string
    {
        return strtolower(class_basename($this->related));
    }

}