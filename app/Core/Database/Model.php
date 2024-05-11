<?php

namespace app\Core\Database;

use App\Core\Arrayable;
use App\Core\Collecting\ModelCollection;
use app\Core\Database\Relations\BelongsTo;
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
        $model = new static();
        $query = $model->toSql();
        $results = $model->db->execute($query)->get();
        return $model->mapResultsToModel($results);
    }

    public function get(): ModelCollection
    {
       // dd($this->query->toSql());
        $results = $this->db->execute(
            $this->toSql(),
            $this->getBindings(),
        )->get();
        return $this->mapResultsToModel($results);
    }

    public function mapResultsToModel(array $results): ModelCollection
    {
        $models = array_map(function ($result) {
            $model = new static();
            $relatedTables = [];
            foreach ($result as $key => $value) {
                if (str_contains($key, '_id')) {
                    $relation = str_replace('_id', '', $key);
                    if (method_exists($model, $relation)) {
                        $relatedModel = $model->$relation();
                        $relatedTable = $relatedModel->getRelatedTable();
                        $relatedTables[] = $relatedTable;

                        $model->$relation = [];

                        foreach ($result as $relatedKey => $relatedValue) {
                            if (str_starts_with($relatedKey, $relatedTable . '_')) {
                                $attribute = str_replace($relatedTable . '_', '', $relatedKey);
                                $model->attributes[$relation][$attribute] = $relatedValue;
                                unset($model->attributes[$relatedKey]);
                            }
                        }

                        if (isset($model->{$relatedTable . '_id'})) {
                            unset($model->{"{$relatedTable}_id"});
                        }
                    }
                } else {
                    $model->$key = $value;
                }
            }

            foreach ($relatedTables as $relatedTable) {
                foreach ($model->attributes as $key => $value) {
                    if (str_starts_with($key, $relatedTable . '_')) {
                        unset($model->attributes[$key]);
                    }
                }
            }
            return $model;
        }, $results);

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

}