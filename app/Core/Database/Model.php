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
    protected object $pivot;

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
            // if the relation is a ModelCollection, return it directly
            if ($this->relations[$name] instanceof ModelCollection) {
                return $this->relations[$name];
            }
            // if the relation is a single model, return it in an array
            if ($this->relations[$name] instanceof self) {
                return new ModelCollection([$this->relations[$name]]);
            }
            // if only 1 array key, return a single model collection
            return new ModelCollection($this->relations[$name]);
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
        if (isset($this->pivot)) {
            $this->attributes['pivot'] = (array)$this->pivot;
        }
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

    public function hydrate(array $results): ModelCollection
    {
        $models = [];

        $lastRowsLookup = [];

        foreach ($results as $row) {
            $modelId = $row['id'];

            $modelIndex = $this->searchModelsById($models, $modelId);
            if ($modelIndex === null) {
                $modelInstance = new static;
                $modelInstance->id = $modelId;
                $models[] = $modelInstance;
                $modelIndex = array_key_last($models);
            }

            $currentModel = $models[$modelIndex];
            $currentLookup = [];
            $pivotColumns = [];

            foreach ($row as $column => $value) {
                if ($column !== 'id') {
                    $parts = explode('_', $column);
                    $relation = method_exists($models[$modelIndex], $parts[0])
                        ? $parts[0]
                        : $this->convertToSingular($parts[0]);

                    if ($value === null) {
                        continue;
                    }

                    if ($relation !== 'pivot' && end($parts) === 'id' && count($parts) > 1) {

                        $relatedModelClass = "App\\Models\\" . ucfirst($this->convertToSingular($parts[0]));

                        if (count($parts) === 2) {
                            // Direct relation (e.g., products_id)
                            if (!$this->relationModelExists($currentModel, $relation, $value)) {
                                $relatedModel = new $relatedModelClass;
                                $relatedModel->id = $value;

                                if (!isset($currentModel->relations[$relation])) {
                                    $currentModel->relations[$relation] = [];
                                }

                                $currentModel->relations[$relation][] = $relatedModel;
                                $this->addRelationToLookup($currentLookup, $relation, $relatedModel);
                            }
                        } else if (count($parts) === 3) {
                            // make sure the nested relation wasn't already set on a previous iteration
                            // check both singular and plural forms
                            $existingRelation = $this->searchLookupReturnModel($lastRowsLookup, $this->convertToSingular($parts[1]));
                            if (!$existingRelation) {
                                $existingRelation = $this->searchLookupReturnModel($lastRowsLookup, $this->convertToPlural($parts[1]));
                            }
                            // we need to allow for the fact the some of the time, we might be getting the same model
                            // on subsequent iterations, for example if 2 products have the same category
                            // in this case we need to allow it, so what we can check is that $relatedModel->id is different
                            // from the $related model in the lookup
                            if ($existingRelation) {
                                $lastKey = array_key_last($currentLookup);
                                $currentLastItem = end($currentLookup[$lastKey]);
                                $lastItem = end($lastRowsLookup[$lastKey]);
                            }
                            if (!$existingRelation || $existingRelation->id !== $value || $lastItem->id !== $currentLastItem->id) {
                                // don't need to create a new relation, just set the property
                                $nestedRelation = method_exists($relatedModel, $parts[1])
                                    ? $parts[1]
                                    : $this->convertToSingular($parts[1]);

                                if (!$this->relationModelExists($relatedModel, $nestedRelation, $value)) {
                                    $nestedRelationModelClass = "App\\Models\\" . ucfirst($nestedRelation);
                                    $nestedRelationModel = new $nestedRelationModelClass;
                                    $nestedRelationModel->id = $value;

                                    if (!isset($relatedModel->relations[$nestedRelation])) {
                                        $relatedModel->relations[$nestedRelation] = [];
                                    }

                                    $relatedModel->relations[$nestedRelation][] = $nestedRelationModel;
                                    $this->addRelationToLookup($currentLookup, $nestedRelation, $nestedRelationModel);
                                }
                            }
                        } else {
                            // TODO: handle recursive n number of nested relations
                            //  I think we can use the $lastRowLookup & $currentLookup to handle this
                        }
                    } else if ($relation === 'pivot') {
                        $pivotColumns[$column] = $value;
                    } else {
                        $this->setProperty($currentModel, $parts, $value, $relation, $currentLookup);
                    }
                }
            }
            if (!empty($pivotColumns)) {
                $this->mapPivotColumns($pivotColumns, $currentLookup);
            }
            $lastRowsLookup = $currentLookup;
        }

        return new ModelCollection($models);
    }

    private function relationModelExists(self $currentModel, string $relation, int $id): bool
    {
        if (!isset($currentModel->relations[$relation])) {
            return false;
        }

        foreach ($currentModel->relations[$relation] as $relatedModel) {
            if ($relatedModel->id === $id) {
                return true;
            }
        }

        return false;
    }

    private function setProperty(&$model, $parts, $value, $relation, &$currentLookup): void
    {
        if (count($parts) === 1) {
            $model->{$parts[0]} = $value;
        } elseif (count($parts) === 2) {
            if ($this->checkRelationNameSetOnModel($model, $relation)) {
                $relatedModel = $this->searchLookupReturnModel($currentLookup, $relation);
                if ($relatedModel) {
                    $relatedModel->{$parts[1]} = $value;
                }
            } else {
                $property = implode('_', $parts);
                $model->{$property} = $value;
            }
        } elseif (count($parts) === 3) {
            $relatedModel = $this->searchLookupReturnModel($currentLookup, $relation);
            if ($relatedModel) {
                $nestedRelation = $this->convertToSingular($parts[1]);
                //method_exists($model, $parts[1]) ? $parts[1] :
                //$this->convertToSingular($parts[1]);
                if (method_exists($relatedModel, $nestedRelation)) {
                    dd('method exists');
                    $nestedModels = $relatedModel->relations[$nestedRelation];
                    $nestedModel = end($nestedModels);
                    $nestedModel->{$parts[2]} = $value;
                } elseif (method_exists($relatedModel, $parts[1])) {
                    dd('method exists part 1');
                    $nestedModels = $relatedModel->relations[$parts[1]];
                    $nestedModel = end($nestedModels);
                    $nestedModel->{$parts[2]} = $value;
                } else {
                    $property = implode('_', array_slice($parts, 1));
                    $relatedModel->{$property} = $value;
                }
            }
        }
    }

    private function mapPivotColumns(array $pivotColumns, array &$currentLookup): void
    {
        $relationNames = array_unique(array_map(fn($column) => explode('_', $column)[1], array_keys($pivotColumns)));

        foreach ($relationNames as $relationName) {
            // Check for both singular and plural forms
            $relatedModel = $this->checkForRelation($relationName, $currentLookup);

            if ($relatedModel) {
                foreach ($pivotColumns as $column => $value) {
                    $parts = explode('_', $column);
                    if (!isset($relatedModel->pivot)) {
                        $relatedModel->pivot = new stdClass;
                    }

                    $property = implode('_', array_slice($parts, 1));
                    $relatedModel->pivot->{$property} = $value;
                }
            }
        }
    }

    public function checkForRelation(mixed $relationName, array &$currentLookup): ?self
    {
        $possibleRelations = [$relationName, $this->convertToPlural($relationName), $this->convertToSingular($relationName)];

        foreach ($possibleRelations as $relation) {
            $relatedModel = $this->searchLookupReturnModel($currentLookup, $relation);
            if ($relatedModel) {
                break;
            }
        }
        return $relatedModel ?? null;
    }

    private function searchModelsById(array $models, $id): ?int
    {
        // NOTE: tested working
        foreach ($models as $key => $model) {
            if ($model->id === $id) {
                return $key;
            }
        }
        return null;
    }

    private function searchLookupReturnModel(array &$currentLookup, string $name): ?self
    {
        // NOTE: tested working
        foreach ($currentLookup as $relationName => $relation) {
            if ($relationName === $name) {
                // return the last model in the array
                return end($relation);
            }
        }
        return null;
    }

    private function checkRelationNameSetOnModel(self $model, string $relation): bool
    {
        // NOTE: tested working
        return isset($model->relations[$relation]);
    }

    private function addRelationToLookup(array &$currentLookup, string $relation, self $model): void
    {
        if (!isset($currentLookup[$relation])) {
            $currentLookup[$relation] = [];
        }

        $currentLookup[$relation][] = $model;
    }

    private function convertToSingular(string $name): string
    {
        if (str_ends_with($name, 'ies')) {
            return substr($name, 0, -3) . 'y';
        }

        if (str_ends_with($name, 's')) {
            return substr($name, 0, -1);
        }
        return $name;
    }

    private function convertToPlural(string $name): string
    {
        if (str_ends_with($name, 'y') && !str_ends_with($name, 'ay') && !str_ends_with($name, 'ey') && !str_ends_with($name, 'iy') && !str_ends_with($name, 'oy') && !str_ends_with($name, 'uy')) {
            return substr($name, 0, -1) . 'ies';
        }

        if (!str_ends_with($name, 's')) {
            return $name . 's';
        }
        return $name;
    }

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


    // add a load method so that we can lazily load the relations
    public function load(string $relation): void
    {
        // Check if a method with the same name as the relation exists in the model
        if (method_exists($this, $relation)) {
            // Call the relation method to get the Relation object
            $relationObject = $this->$relation();

            // Check if the method returns a Relation object
            if ($relationObject instanceof Relation) {

                // Load the related models
                $relatedModels = $relationObject->query()->get();

                // Store the related models in the relations property of the model
                $this->relations[$relation] = $relatedModels->getItems();
            }
        }
    }

}