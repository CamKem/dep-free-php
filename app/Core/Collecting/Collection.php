<?php

namespace App\Core\Collecting;

use App\Core\Arrayable;
use InvalidArgumentException;
use JsonException;
use JsonSerializable;
use Traversable;

/** Hold array items to perform operations on */
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

        return new static($this->items);
    }

    public function first(?callable $callback = null, mixed $default = null): mixed
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

    public function contains(mixed $value): bool
    {
        return in_array($value, $this->items, true);
    }

    public function merge(mixed $items): self
    {
        $validatedItems = array_filter($items, $this->getArrayableItems($items));
        return new static(array_merge($this->items, $validatedItems));
    }

    public function filter(callable $callback): self
    {
        return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function toArray(): array
    {
        return $this->getArrayableItems($this->items);
    }

    /** @throws JsonException */
    public function toJson(): string
    {
        return json_encode($this->items, JSON_THROW_ON_ERROR);
    }

    /** @throws JsonException */
    public function jsonSerialize(): string
    {
        return $this->toJson();
    }

    public function getArrayableItems($items): array
    {
        return match ($items) {
            is_array($items) => $items,
            $items instanceof self, $items instanceof Arrayable => $items->toArray(),
            $items instanceof JsonSerializable => $items->jsonSerialize(),
            $items instanceof Traversable => iterator_to_array($items),
            default => (array)$items,
        };
    }

    public function all(): array
    {
        return $this->items;
    }

    public function groupBy(string $key): self
    {
        $grouped = [];

        foreach ($this->items as $item) {
            $grouped[$item[$key]][] = $item;
        }

        return new static($grouped);
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function sortBy(callable|string $value, string $order = 'asc'): self
    {
        if (is_string($value)) {
            $callback = static fn($item) => is_object($item) ? $item->$value : $item[$value];
        } elseif (is_callable($value)) {
            $callback = $value;
        } else {
            throw new InvalidArgumentException('Invalid value provided for sortBy');
        }

        $items = $this->items;

        uasort($items, static function ($a, $b) use ($callback, $order) {
            $result = $callback($a) <=> $callback($b);
            return $order === 'desc' ? -$result : $result;
        });

        return new static($items);
    }

    public function min(?string $key = null): mixed
    {
        if ($this->isEmpty()) {
            return null;
        }

        if ($key === null) {
            return min($this->items);
        }

        return min(array_column($this->items, $key));
    }

    public function values(): self
    {
        return new static(array_values($this->items));
    }

    public function keys(): self
    {
        return new static(array_keys($this->items));
    }

     public function take(int $limit): self
    {
        return new static(array_slice($this->items, 0, $limit));
    }

    public function __serialize(): array
    {
        return $this->items;
    }

    public function __unserialize(array $data): void
    {
        $this->items = $data;
    }

}