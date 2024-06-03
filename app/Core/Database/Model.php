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
    protected array $relations = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    public function __get(string $name): mixed
    {
        // Check if the relation is already loaded
        if (!empty($this->relations) && array_key_exists($name, $this->relations)) {
            if ($this->relations[$name] instanceof self) {
                return new ModelCollection([$this->relations[$name]]);
            }
            // if only 1 array key, return a single model collection
            return new ModelCollection($this->relations[$name]);
        }

        // Check if a method with the same name as the property exists
        if (method_exists($this, $name)) {
            // Call the relation method and store the result
            //(Model or ModelCollection) in the relations array
            $relation = $this->$name();

            // only if the method is being called as
            // if the method is being called as a property, we should not call it

            // Check if the method returns a Relation object
            if ($relation instanceof Relation) {
                // Load the related models
                $this->relations[$name] = $relation->query()->toSql();
                // Return the related models
                return $this->relations[$name] ?? null;
            }
        }

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

    public function getRelated(): array
    {
        return $this->relations;
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

    public function hasManyThrough(string $relatedModel, string $pivotTable, string $foreignKey, string $relatedKey, bool $withPivot = false): HasManyThrough
    {
        return new HasManyThrough($this, new $relatedModel, new $pivotTable, $foreignKey, $relatedKey, $withPivot);
    }


    // MAP MODELS:
    //we have 4 types of scenarios
    // 1: a model with no related models, that is a single row in the database
    // 2: multiple rows for the same model, with no related models
    // 3: one or more row for the same model, with no related models
    // 4: one or more row for the same model, with one or more related models
    // we need to handle each of these scenarios
    public function hydrate(array $results): ModelCollection
    {
        $models = [];
        $relatedModels = [];
        $firstRowId = $results[0]['id'] ?? null;

        foreach ($results as $row) {
            // get the id of the main model
            $mainModelId = $row['id'];
            $mainModelData = [];
            $relatedModelData = [];

            // TODO need to work out how we will map the data for pivot columns, this is the last piece of the puzzle
            // NOTE: we can then refactor the code to make it abstract and reusable

            // TODO: change how we store the models with the id of the model as the key.
            //  rather we can look at the id in the instances of the model
            //  that we can maintain normal array keys for the models

            // check if the model has already been created
            if (!isset($models[$mainModelId])) {
                // loop through the columns in the row
                foreach ($row as $column => $value) {
                    // if the column is not the id, and contains _id, it is a foreign key
                    if ($column !== 'id' && str_contains($column, '_id')) {
                        // Identify related model by _id suffix
                        $relation = substr($column, 0, strpos($column, '_id'));
                        // drop the s off the end of the relation name, or ies and add a y
                        if (str_ends_with($relation, 'ies')) {
                            $relation = substr($relation, 0, -3) . 'y';
                        } elseif (str_ends_with($relation, 's')) {
                            $relation = substr($relation, 0, -1);
                        }
                        // remove the relation prefix from the column name
                        $column = substr($column, strpos($column, 'id'));
                        $relatedModelData[$relation][$column] = $value;
                    } else {
                        // if the column has the prefix that is the same as any of the related models
                        // map them under the related model & remove the prefix
                        foreach ($relatedModelData as $relation => $data) {
                            // if the column has the prefix that is the same as any of the related models
                            // map them under the related model & remove the prefix
                            if (str_starts_with($column, $relation . '_')) {
                                $relatedModelData[$relation][substr($column, strlen($relation) + 1)] = $value;
                                unset($row[$column]);
                                // else if the column doesn't have a valid $relation prefix it should just be mapped normally.
                            }
                            // if the column has a prefix with the relation name and an s
                            // map them under the related model & remove the prefix
                            if (str_starts_with($column, $relation . 's_')) {
                                $relatedModelData[$relation][substr($column, strlen($relation) + 2)] = $value;
                                unset($row[$column]);
                            }
                        }

                        $relation = $relation ?? null;
                        // skip any columns that are related to the model, when mapping the main model data
                        if (!str_starts_with($column, $relation . '_') && !str_starts_with($column, $relation . 's_')) {
                            $mainModelData[$column] = $value;
                        }
                    }
                }

                $models[$mainModelId] = new static($mainModelData);

                foreach ($relatedModelData as $relation => $data) {
                    // we can start model class with a capital letter
                    // higher up in the logic, rather than do it here.
                    // we can also use the $relation variable to get the class name
                    $relationModelClass = "App\\Models\\" . ucfirst($relation);
                    $relatedModels[$mainModelId][$relation][] = new $relationModelClass($data);
                }
                // else if the model has already been created,
                //we need to add the related models to the model
            } else {
                // loop through the columns in the row
                foreach ($row as $column => $value) {
                    // if the column is not the id, and contains _id, it is a foreign key
                    if ($column !== 'id' && str_contains($column, '_id')) {
                        // Identify related model by _id suffix
                        $relation = substr($column, 0, strpos($column, '_id'));
                        // drop the s off the end of the relation name, or ies and add a y
                        if (str_ends_with($relation, 'ies')) {
                            $relation = substr($relation, 0, -3) . 'y';
                        } elseif (str_ends_with($relation, 's')) {
                            $relation = substr($relation, 0, -1);
                        }
                        // remove the relation prefix from the column name
                        $column = substr($column, strpos($column, 'id'));
                        $relatedModelData[$relation][$column] = $value;
                    } else {
                        // if the column has the prefix that is the same as any of the related models
                        // map them under the related model & remove the prefix
                        foreach ($relatedModelData as $relation => $data) {
                            if (str_starts_with($column, $relation . '_')) {
                                $relatedModelData[$relation][substr($column, strlen($relation) + 1)] = $value;
                                unset($row[$column]);
                                // else if the column doesn't have a valid $relation prefix it should just be mapped normally.
                            }
                            if (str_starts_with($column, $relation . 's_')) {
                                $relatedModelData[$relation][substr($column, strlen($relation) + 2)] = $value;
                                unset($row[$column]);
                            }
                        }
                    }
                }

                foreach ($relatedModelData as $relation => $data) {
                    // we can start model class with a capital letter
                    // higher up in the logic, rather than do it here.
                    // we can also use the $relation variable to get the class name
                    $relationModelClass = "App\\Models\\" . ucfirst($relation);
                    $relatedModels[$firstRowId][$relation][] = new $relationModelClass($data);
                }
            }
        }

        // if the related models are not empty, we need to add them to the main model
        if (!empty($relatedModels)) {
            //dd($relatedModels);
            foreach ($models as $id => $model) {
                // add to model's relation property which they are related to
                foreach ($relatedModels[$id] as $relation => $relationModels) {
                    $model->relations[$relation] = $relationModels;
                }
            }

        }

        return new ModelCollection($models);
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

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

}