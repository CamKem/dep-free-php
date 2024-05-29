<?php

namespace app\Core\Database;

use App\Core\Arrayable;
use App\Core\Collecting\ModelCollection;
use app\Core\Database\Relations\BelongsTo;
use app\Core\Database\Relations\HasMany;
use app\Core\Database\Relations\HasManyThrough;
use app\Core\Database\Relations\HasOne;
use JsonSerializable;

class Model implements Arrayable, JsonSerializable
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $attributes = [];

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

    public function query(): QueryBuilder
    {
        return new QueryBuilder($this);
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function hydrate(array $results): ModelCollection
    {
        return $this->mapResultsToModel($results);
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

    public function belongsTo(string $relatedModel): BelongsTo
    {
        return new BelongsTo($this, new $relatedModel);
    }

    public function hasOne(string $relatedModel): HasOne
    {
        return new HasOne($this, new $relatedModel);
    }

    public function hasMany(string $relatedModel): HasMany
    {
        return new HasMany($this, new $relatedModel);
    }

    public function hasManyThrough(string $relatedModel, string $pivotTable, string $foreignKey, string $relatedKey): HasManyThrough
    {
        return new HasManyThrough($this, new $relatedModel, new $pivotTable, $foreignKey, $relatedKey);
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

                        // TODO: work out how to handle mapping the related model as a Model instance
                        //  in it's own sub-collection

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

    // TODO: work out why this is not ensuring that only the attributes are serialized
    //  the base class is being serialized as well, which is not what we want
    // NOTE: URGENT
    //  url: https://www.php.net/manual/en/language.oop5.magic.php#object.serialize
    public function __serialize(): array
    {
        return $this->getAttributes();
    }

    public function __unserialize(array $data): void
    {
        $this->attributes = $data;
    }

    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
}