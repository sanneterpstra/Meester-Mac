<?php

namespace App\Data;

use Spatie\LaravelData\Concerns\WireableData;
use Spatie\LaravelData\Data;

class ProductOptionData extends Data
{
    use WireableData;

    public function __construct(
        public int $id,
        public string $name,
        public string $label,
        public int $position,
        public string $handle,
        public $values,
    ) {
    }

    public static function fromModel(\Lunar\Models\ProductOption $option): self
    {
        return new self(
            $option->id,
            $option->translate('name'),
            $option->translate('label'),
            $option->position,
            $option->handle,
            ProductOptionValueData::collection($option->values->all()),
        );
    }
}
