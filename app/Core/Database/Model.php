<?php

namespace app\Core\Database;

use App\Core\Arrayable;
use App\Core\Collecting\ModelCollection;
use app\Core\Database\Relations\BelongsTo;
use app\Core\Database\Relations\HasMany;
use app\Core\Database\Relations\HasManyThrough;
use app\Core\Database\Relations\HasOne;
use JsonSerializable;
use stdClass;

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

    // TODO: change how we store the models with the id of the model as the key.
    //  rather we can look at the id in the instances of the model
    //  that we can maintain normal array keys for the models

    // NOTE: we can then refactor the code to make it abstract and reusable
    // TODO: Problem to solve, allow for n number of nested relations, recursively
    public function hydrate(array $results): ModelCollection
    {
        $models = [];
        foreach ($results as $row) {
            // get the id of the model
            $ModelId = $row['id'];

            // first we should check if the model has already been created
            if (!isset($models[$ModelId])) {
                // if the model has not been created
                // lets instantiate the model
                $models[$ModelId] = new static;
                // and set the id of the model
                $models[$ModelId]->id = $ModelId;
            }

            $currentRelationId = null;
            $currentRelationName = null;
            $relations = [];
            $currentNestedRelationId = null;

            // we should loop through & build the relations
            foreach ($row as $column => $value) {
                // TODO: need a better way to store the current relation name & id, because there might be multiple in a row
                // if the column is not the id:
                if ($column !== 'id') {
                    // split it into parts
                    $parts = explode('_', $column);
                    // check if the method exists on the model
                    if (method_exists($models[$ModelId], $parts[0])) {
                        $relation = $parts[0];
                    } else {
                        $relation = $this->convertToSingular($parts[0]);
                    }
                    $relationModelName = $this->convertToSingular($parts[0]);
                    // if the last part of the column is id, we have a foreign key
                    if (end($parts) === 'id' && count($parts) > 1) {
                        if (count($parts) > 2) {
                            $class = $models[$ModelId]->relations[$relation][$currentRelationId] ?? new StdClass;
                            if (method_exists($class, $parts[1])) {
                                $nestedRelation = $parts[1];
                            } else {
                                $nestedRelation = $this->convertToSingular($parts[1]);
                            }
                            $nestedRelationModelName = $this->convertToSingular($parts[1]);
                            // we might have a nested relation
                            if (!isset($models[$ModelId]->relations[$relation][$currentRelationId]->relations[$nestedRelation][$value])) {
                                if (isset($models[$ModelId]->relations[$relation][$currentRelationId])) {
                                    $nestedRelationModel = "App\\Models\\" . ucfirst($nestedRelationModelName);
                                    $models[$ModelId]->relations[$relation][$currentRelationId]->relations[$nestedRelation][$value] = new $nestedRelationModel;
                                    $model = $models[$ModelId]->relations[$relation][$currentRelationId]->relations[$nestedRelation][$value];
                                    $model->id = $value;
                                    $currentNestedRelationId = $value;
                                    // find the $relation in the $relation in the $relations array
                                    // it will be stored inside the $relations array as an array
                                    $relations[$relation][$nestedRelation][] = $value;
                                }
                            }
                        } else {
                            // we might have a relation
                            //$relation = $this->convertToSingular($parts[count($parts) - 2]);
                            // first we need to check if the relation exists
                            if (!isset($models[$ModelId]->relations[$relation][$value])) {
                                $relatedModel = "App\\Models\\" . ucfirst($relationModelName);
                                $models[$ModelId]->relations[$relation][$value] = new $relatedModel;
                                $model = $models[$ModelId]->relations[$relation][$value];
                                $model->id = $value;
                                $relations[$relation][] = $value;
                                $currentRelationName = $relation;
                                $currentRelationId = $value;
                            }
                        }
                    }
                }
            }

            // now we should loop through again an map the data now the relations are created
            foreach ($row as $column => $value) {
                // if the column is not the id:
                if ($column !== 'id') {
                    // split it into parts
                    $parts = explode('_', $column);

                    if (method_exists($models[$ModelId], $parts[0])) {
                        $relation = $parts[0];
                    } else {
                        $relation = $this->convertToSingular($parts[0]);
                    }
                   // $relation = $this->convertToSingular($parts[0]);

                    // TODO: work out a better way to check for existence of a relationship & then set the value,
                    //  this will ensure that we can have n number of nested relations
                    //  using: $this->keyExistsInNestedArray($this->convertToSingular($parts[0]), $value, $relations))

                    if (isset($parts[1])) {
                        $class = $models[$ModelId]->relations[$relation][$currentRelationId] ?? new StdClass;
                        if (method_exists($class, $parts[1])) {
                            $nestedRelation = $parts[1];
                        } else {
                            $nestedRelation = $this->convertToSingular($parts[1]);
                        }
                    }

                    if (count($parts) === 1) {
                        $property = $parts[0];
                        $models[$ModelId]->$property = $value;
                    }
                    if (count($parts) === 2) {
                        // it's a related model column
                        if (isset($models[$ModelId]->relations[$relation][$currentRelationId])) {
                            // get the value of $parts[1] and set it as a property of the related model
                            $property = $parts[1];
                            $models[$ModelId]->relations[$relation][$currentRelationId]->$property = $value;
                        } elseif ($relation === 'pivot') {
                            // if the relation is a pivot table, we should store the value in the attributes array
                            $key = implode('_', $parts);
                            $models[$ModelId]->relations[$currentRelationName][$currentRelationId]->attributes[$key] = $value;
                        } else {
                            $related = $relations;
                            // if the relation doesn't exist, we should just map the value to the main model
                            // TODO: we need to come up with a better way to use $relations.
                            if (!$this->keyExistsInNestedArray($relation, $value, $relations)) {
                                if (end($parts) !== 'id') {
                                    $property = $parts[0] . '_' . $parts[1];
                                    $models[$ModelId]->$property = $value;
                                }
                            }
                        }
                    }
                    if (count($parts) === 3) {
                        if (isset($models[$ModelId]->relations[$relation][$currentRelationId]->relations[$nestedRelation][$currentNestedRelationId])) {
                            if ($parts[0] !== 'pivot') {
                                // it's a nested related model column
                                // get the value of $parts[2] and set it as a property of the nested related model
                                $property = $parts[2];
                                $models[$ModelId]->relations[$relation][$currentRelationId]->relations[$nestedRelation][$currentNestedRelationId]->$property = $value;
                            }
                        } else {
                            // check if the value should be put on a relation with the 2 last parts joined
                            if ($relation !== 'pivot' && $models[$ModelId]->relations[$relation][$currentRelationId]) {
                                $property = $parts[1] . '_' . $parts[2];
                                $models[$ModelId]->relations[$relation][$currentRelationId]->$property = $value;
                            } elseif ($relation === 'pivot') {
                                $key = implode('_', $parts);
                                $models[$ModelId]->relations[$currentRelationName][$currentRelationId]->attributes[$key] = $value;
                            } else {
                                // if the relation doesn't exist, we should just join the parts and map the value to the main model
                                $property = $parts[0] . '_' . $parts[1] . '_' . $parts[2];
                                $models[$ModelId]->$property = $value;
                            }
                        }
                    }
                }
            }
        }

        //dd($models);
        return new ModelCollection($models);
    }

    public function keyExistsInNestedArray($key, $value, $array): bool
    {
        // Check if the key exists in the current level and the value matches
        if (array_key_exists($key, $array) && in_array($value, $array[$key], true)) {
            return true;
        }

        // If the key does not exist in the current level or the value doesn't match, check the next level
        foreach ($array as $k => $v) {
//            dd($k,
//                $v,
//                array_key_exists($k, $array),
//                $value,
//                $array[$k],
//                in_array($value, $array[$k])
//            );
            if (is_array($v) && $this->keyExistsInNestedArray($k, $value, $v)) {
                return true;
            }
        }

        // If the key does not exist in any level or the value doesn't match, return false
        return false;
    }

    private function convertToSingular($name): string
    {
        if (str_ends_with($name, 'ies')) {
            return substr($name, 0, -3) . 'y';
        }
        if (str_ends_with($name, 's')) {
            return substr($name, 0, -1);
        }
        return $name;
    }

// TODO: work out why this is not ensuring that only the attributes are serialized
//  the base class is being serialized as well, which is not what we want
// NOTE: URGENT
//  url: https://www.php.net/manual/en/language.oop5.magic.php#object.serialize
    public
    function __serialize(): array
    {
        return $this->getAttributes();
    }

    public
    function __unserialize(array $data): void
    {
        $this->attributes = $data;
    }

    public
    function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

}