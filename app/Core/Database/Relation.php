<?php

namespace App\Core\Database;

abstract class Relation
{
    public function __construct(
        protected Model $parent,
        protected Model $related
    ){}

    abstract public function getParentTable(): string;

    abstract public function getForeignKey(): string;

    abstract public function getRelatedTable(): string;

    abstract public function getRelatedKey(): string;

    abstract public function query(): QueryBuilder;
}