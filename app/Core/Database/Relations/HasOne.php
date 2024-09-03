<?php

namespace App\Core\Database\Relations;

use App\Core\Database\Model;
use App\Core\Database\QueryBuilder;
use App\Core\Database\Relation;
use Override;

class HasOne extends Relation
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
    public function getForeignKey(): string
    {
        return strtolower(class_basename($this->parent)) . '_id';
    }

    #[Override]
    public function getRelatedTable(): string
    {
        return $this->related->getTable();
    }

    #[Override]
    public function getRelatedKey(): string
    {
        return $this->related->getPrimaryKey();
    }

    #[Override]
    public function query(): QueryBuilder
    {
        return $this->related->query()
            ->setRelation($this)
            ->where(
                $this->getForeignKey(),
                $this->parent->id
            );
    }

    public function getRelationName(): string
    {
        return strtolower(class_basename($this->related));
    }

}