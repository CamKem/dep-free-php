<?php

namespace App\Core\Collecting;

use app\Core\Database\Model;
use Override;

class ModelCollection extends Collection
{

    // recursively convert the collection to an array
    #[Override]
    public function toArray($data = null): array
    {
        $items = $data ?? $this->items;

        if (empty($items)) {
            return [];
        }

        $array = [];
        foreach ($items as $key => $value) {
            if ($value instanceof Model) {
                $array[$key] = $value->toArray();
                $relations = $value->getRelated();
                if (!empty($relations)) {
                    foreach ($relations as $relation => $models) {
                        if ($models instanceof Model) {
                            $array[$key][$relation] = $models->toArray();
                        } elseif (is_array($models)) {
                            $array[$key][$relation] = $this->toArray($models);
                        }
                    }
                }
            }
        }
        return $array;
    }

    // if there is only 1 item in the collection, allow property access
    public function __get($name)
    {
        if (count($this->items) === 1) {
            return $this->items[0]->$name;
        }
    }

}