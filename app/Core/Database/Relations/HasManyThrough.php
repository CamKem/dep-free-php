<?php

namespace App\Core\Database\Relations;

use App\Core\Database\Model;
use App\Core\Database\QueryBuilder;
use App\Core\Database\Relation;
use Override;

class HasManyThrough extends Relation
{

    public function __construct(
        protected Model  $parent,
        protected Model  $related,
        protected Model  $pivot,
        protected string $foreignKey,
        protected string $relatedKey,
        public bool      $withPivot = false
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
    public function query($where = true): QueryBuilder
    {
        $query = $this->related->query()
            ->setRelation($this)
            ->select("{$this->getRelatedTable()}.*");

        if ($this->withPivot) {
            // TODO: make sure this works & return the pivot columns
            //  to be mapped to the related model
            $query->select("pivot.*");
        }

        $query->join("{$this->pivot->getTable()} AS pivot", "pivot.{$this->getRelatedKey()}", "=", "id")
            ->join("{$this->getParentTable()} AS origin", "origin.id", "=", "pivot.{$this->getForeignKey()}");

        if ($where) {
            $query->where("origin.id", "=", $this->getParentId());
        }

        return $query;
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
            ])->save();
        }
    }

    // detach items from the pivot model
    // TODO: to attaches & deletes that use whereIn, so it's not n+1 loop
    //    public function detach(array $items): void
//    {
//        $ids = implode(", ", array_map(fn($item) => $item->id, $items));
//        $this->pivot->query()
//                ->where($this->getForeignKey(), $this->getParentId())
//                ->whereIn($this->getRelatedKey(), $ids)
//    }
    public function detach(array $items): void
    {
        foreach ($items as $item) {
            $this->pivot->query()
                ->where($this->getForeignKey(), $this->getParentId())
                ->where($this->getRelatedKey(), $item)
                ->delete()->save();
        }
    }

    public function sync(array $items): void
    {
        $this->pivot->query()
            ->where($this->getForeignKey(), $this->getParentId())
            ->delete()->save();
        $this->attach($items);
    }

    public function getRelationName(): string
    {
        return strtolower(class_basename($this->related)) . 's';
    }

}