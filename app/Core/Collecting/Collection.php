<?php

namespace App\Core\Collecting;

use App\Core\Arrayable;
use JsonException;
use JsonSerializable;
use Traversable;

/**
 * Object to hold array items and perform operations on them

 */
class Collection implements Arrayable, JsonSerializable
{

    protected array $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function put(string $key, mixed $value): self
    {
        $this->items[$key] = $value;
        return $this;
    }

    public function each(callable $callback): self
    {
        foreach ($this as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }

        return $this;
    }

    /**
     * Get the first item from the collection.
     * Optionally, a callback can be passed to filter the items
     * and return the first item that matches the filter.
     *
     * @param callable|null $callback
     * @param mixed $default
     * @return mixed
     */
    public function first(callable|null $callback = null, mixed $default = null): mixed
    {
        if ($callback === null) {
            if (empty($this->items)) {
                return $default;
            }
            return reset($this->items);
        }

        foreach ($this->items as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        // return the value of the key if it exists, otherwise return the default value
        return $this->items[$key] ?? $default;
    }

    public function map(callable $callback): self
    {
        $keys = array_keys($this->items);
        $items = array_map($callback, $this->items, $keys);
        return new static(array_combine($keys, $items));
    }

    public function merge(mixed $items): self
    {
        $validatedItems = array_filter($items, $this->getArrayableItems($items));
        return new static(array_merge($this->items, $validatedItems));
    }

    public function filter(callable $callback): self
    {
        array_filter($this->items, $callback);
        return $this;
    }

    /**
     * Get the number of items in the collection.
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Get the items as a plain array.
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Serialize the instance to JSON.
     *
     * @throws JsonException
     */
    public function toJson(): string
    {
        return $this->jsonSerialize();
    }

    /**
     * @throws JsonException
     */
    public function jsonSerialize(): string
    {
        return json_encode($this->items, JSON_THROW_ON_ERROR);
    }

    /**
     * Check the items to see if they are arrayable.
     * Only arrayable items can be collected.
     */
    public function getArrayableItems($items): array
    {
        if (is_array($items)) {
            return $items;
        }

        return match (true) {
            $items instanceof self, $items instanceof Arrayable => $items->toArray(),
            $items instanceof JsonSerializable => $items->jsonSerialize(),
            $items instanceof Traversable => iterator_to_array($items),
            default => (array)$items,
        };
    }

}