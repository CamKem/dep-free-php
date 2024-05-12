<?php

namespace app\Core\Database;

use App\Core\Arrayable;
use App\Core\Collecting\ModelCollection;
use app\Core\Database\Relations\BelongsTo;
use app\Core\Database\Relations\HasMany;
use JsonSerializable;

class Model extends QueryBuilder implements Arrayable, JsonSerializable
{

    protected Database $db;
    protected array $attributes = [];

    public function __construct()
    {
        $this->db = app(Database::class);
        parent::__construct($this->table);
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

    public static function all(): ModelCollection
    {
        $model = static::getInstance();
        $results = $model->db->execute(
            $model->toSql(),
            $model->getBindings(),
        )->get();
        return $model->mapResultsToModel($results);
    }

    public function save(): void
    {
        $this->db->execute(
            $this->toSql(),
            $this->getBindings(),
        );
    }

    public function get(): ModelCollection
    {
        $results = $this->db->execute(
            $this->toSql(),
            $this->getBindings(),
        )->get();
        return $this->mapResultsToModel($results);
    }

    public function first(): ?self
    {
        $results = $this->db->execute(
            $this->toSql(),
            $this->getBindings(),
        )->get();
        if (empty($results)) {
            return null;
        }
        $model = new static();
        foreach ($results[0] as $key => $value) {
            $model->$key = $value;
        }
        return $model;
    }

    public function exists(): bool
    {
        $count = $this->db->execute(
            $this->toSql(),
            $this->getBindings(),
        )->count();
        return $count > 0;
    }

    public function mapResultsToModel(array $results): ModelCollection
    {
        $models = $this->mapModels($results);

        return new ModelCollection($models);
    }

    public function toArray(): array
    {
        return $this->getAttributes();
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function belongsTo(string $relatedModel): BelongsTo
    {
        $relatedModelInstance = new $relatedModel;
        return new BelongsTo($this, $relatedModelInstance);
    }

    public function hasMany(string $relatedModel): HasMany
    {
        $relatedModelInstance = new $relatedModel;
        $this->setInstance($relatedModelInstance);
        return new HasMany($this, $relatedModelInstance);
    }

    /**
     * @param array $results
     * @return Model[]
     */
    public function mapModels(array $results): array
    {
        return array_map(static function ($result) {
            $model = new static();
            $relations = [];
            foreach ($result as $key => $value) {
                if (str_contains($key, '_id')) {
                    $relation = str_replace('_id', '', $key);
                    if (method_exists($model, $relation)) {
                        $relations[] = $relation;
                        $model->$relation = [];

                        foreach ($result as $relatedKey => $relatedValue) {
                            if (str_starts_with($relatedKey, $relation . '_')) {
                                $attribute = str_replace($relation . '_', '', $relatedKey);
                                $model->attributes[$relation][$attribute] = $relatedValue;
                                unset($model->attributes[$relatedKey]);
                            }
                        }

                        if (isset($model->{$relation . '_id'})) {
                            unset($model->{"{$relation}_id"});
                        }
                    }
                } else {
                    $model->$key = $value;
                }
            }

            foreach ($relations as $relation) {
                foreach ($model->attributes as $key => $value) {
                    if (str_starts_with($key, $relation . '_')) {
                        unset($model->attributes[$key]);
                    }
                }
            }
            return $model;
        }, $results);
    }

    public function __serialize(): array
    {
        return $this->attributes;
    }

    public function __unserialize(array $data): void
    {
        $this->attributes = $data;
    }
}