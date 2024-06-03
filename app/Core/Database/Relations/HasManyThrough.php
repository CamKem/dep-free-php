<?php

namespace app\Core\Database\Relations;

use app\Core\Database\Model;
use app\Core\Database\QueryBuilder;
use app\Core\Database\Relation;
use Override;

class HasManyThrough extends Relation
{

    public function __construct(
        protected Model  $parent,
        protected Model  $related,
        protected Model  $pivot,
        protected string $foreignKey,
        protected string $relatedKey,
        public bool $withPivot = false
    ){}

    #[Override]
    public function getParentTable(): string
    {
        return $this->parent->getTable();
    }

    #[Override]
    public function getForeignKey(): string
    {
        return $this->foreignKey;
    }

    #[Override]
    public function getRelatedTable(): string
    {
        return $this->related->getTable();
    }

    #[Override]
    public function getRelatedKey(): string
    {
        return $this->relatedKey;
    }

    public function getPivotTable(): string
    {
        return $this->pivot->getTable();
    }

    public function getParentId(): int
    {
        return $this->parent->id;
    }

    #[Override]
    public function query(): QueryBuilder
    {
        return $this->related->query()
            ->setRelation($this)
            ->select("{$this->getRelatedTable()}.*")
            ->join("{$this->pivot->getTable()} AS pivot", "pivot.{$this->getRelatedKey()}", "=", "id")
            ->join("{$this->getParentTable()} AS origin", "origin.id", "=", "pivot.{$this->getForeignKey()}")
            ->where("origin.id", "=", $this->getParentId());
    }
    public function attach(array $items): void
    {
        foreach ($items as $item) {
            // remove the key with [$this->getRelatedKey()] from the item
            $remainingItems = array_filter((array)$item, fn($key) => $key !== $this->getRelatedKey(), ARRAY_FILTER_USE_KEY);
            $this->pivot->query()->create([
                $this->getForeignKey() => $this->getParentId(),
                $this->getRelatedKey() => $item[$this->getRelatedKey()],
                ...$remainingItems
        }
    }
    // detach items from the pivot model
    public function detach(array $items): void
    {
        foreach ($items as $item) {
            $this->pivot->query()
                ->where($this->getForeignKey(), $this->getParentId())
                ->where($this->getRelatedKey(), $item->id)
                ->delete()->save();
        }
    }

    public function getRelationName(): string
    {
        return strtolower(class_basename($this->related)) . 's';
    }

}