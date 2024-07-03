<?php

namespace App\Data;

use Spatie\LaravelData\Concerns\WireableData;
use Spatie\LaravelData\Data;

class ProductOptionValueData extends Data
{
    use WireableData;

    public function __construct(
        public int $id,
        public ?string $name,
        protected $variants,
    ) {
    }

    public static function fromModel(\Lunar\Models\ProductOptionValue $value): self
    {
        return new self(
            $value->id,
            $value->translate('name'),
            $value->variants,
        );
    }

    public function with(): array
    {
        return [
            'available' => (
                $this->variants->where('purchasable', 'always')
                    ->count()
                ||
                $this->variants->where('purchasable', 'in_stock')
                    ->where('stock', '>', 0)
                    ->count()
            ) ? 1 : 0,
        ];
    }
}
