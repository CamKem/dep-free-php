<?php

namespace App\Core\Collecting;

use App\Core\Database\Model;
use ArrayIterator;
use Iterator;
use Override;

class ModelCollection extends Collection implements Iterator
{

    // recursively convert the collection to an array
    #[Override]
    public function toArray(): array
    {
        if (empty($this->items)) {
            return [];
        }

        $array = [];
        foreach ($this->items as $key => $value) {
            if ($value instanceof Model) {
                $array[$key] = $value->toArray();
                $relations = $value->getRelated();
                if (!empty($relations)) {
                    foreach ($relations as $relation => $models) {
                        if ($models instanceof Model) {
                            // Single related model
                            $array[$key][$relation] = $models->toArray();
                        } elseif ($models instanceof self) {
                            // Collection of related models
                            $array[$key][$relation] = $models->toArray();
                        } elseif (is_array($models)) {
                            $array[$key][$relation] = (new self($models))->toArray();
                        }
                    }
                }
            }
        }
        return $array;
    }

    #[Override]
    public function contains($value): bool
    {
        foreach ($this->items as $item) {
            if ($item instanceof Model) {
                foreach ($item->toArray() as $key => $val) {
                    if ($val === $value) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    // if there is only 1 item in the collection, allow property access
    public function __get($name)
    {
        if (count($this->items) === 1) {
            return array_values($this->items)[0]->$name;
        }
    }

    private int $position = 0;

    public function current(): mixed
    {
        return $this->items[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): mixed
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->items[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function load(string $relation): void
    {
        foreach ($this->items as $item) {
            $item->load($relation);
        }
    }

}