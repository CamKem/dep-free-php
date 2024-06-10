<?php

namespace App\Core\Collecting;

use BadMethodCallException;
use IteratorAggregate;
use Traversable;

class Paginator implements IteratorAggregate
{
    public function __construct(
        protected ModelCollection $items,
        private readonly int $currentPage,
        private readonly int $lastPage,
    )
    {}

    public function __call($method, $arguments)
    {
        if (method_exists($this->items, $method)) {
            return $this->items->{$method}(...$arguments);
        }

        throw new BadMethodCallException("Method {$method} does not exist on ModelCollection.");
    }

    public function items(): ModelCollection
    {
        return $this->items;
    }

    public function pages(): array
    {
        return range(1, $this->lastPage);
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function lastPage(): int
    {
        return $this->lastPage;
    }

    public function nextPageUrl(): ?string
    {
        if ($this->currentPage < $this->lastPage) {
            return request()->getUri() . '?page=' . ($this->currentPage + 1);
        }
        return null;
    }

    public function previousPageUrl(): ?string
    {
        if ($this->currentPage > 1) {
            return request()->getUri() . '?page=' . ($this->currentPage - 1);
        }
        return null;
    }

    public function getIterator(): Traversable
    {
        return $this->items->getIterator();
    }

}