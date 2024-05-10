<?php

namespace App\Core\Collecting;

use Override;

class ModelCollection extends Collection
{
    #[Override]
    public function toArray(): array
    {
        return array_map(static function ($item) {
            return $item->getAttributes();
        }, $this->items);
    }
}