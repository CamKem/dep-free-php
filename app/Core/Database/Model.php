<?php

namespace app\Core\Database;

use App\Core\Arrayable;
use App\Core\Collecting\ModelCollection;
use JsonSerializable;

class Model implements Arrayable, JsonSerializable
{

    protected Database $db;
    protected string $query;

    protected string $table;
    protected array $attributes = [];

    public function __construct()
    {
        $this->query = "select * from $this->table";
        $this->db = app(Database::class);
    }

    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    public function all(): self
    {
        return $this;
    }

    public function where(string $column, mixed $value): self
    {
        $this->query .= " where $column = :value";
        $this->db->bind(compact('value'));
        return $this;
    }

    public function get(): ModelCollection
    {
        $results = $this->db->query($this->query)->get();
        return $this->mapResultsToModel($results);
    }

    private function mapRow(array $row): static
    {
        $model = new static();
        foreach ($row as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }

    private function mapRows(array $rows): ModelCollection
    {
        return new ModelCollection(array_map(function ($row) {
            return $this->mapRow($row);
        }, $rows));
    }

    private function mapResultsToModel(array $results): ModelCollection
    {
        return $this->mapRows($results);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

}