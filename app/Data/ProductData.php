<?php

namespace App\Data;

use Livewire\Wireable;
use Spatie\LaravelData\Concerns\WireableData;
use Spatie\LaravelData\Data;

class ProductData extends Data implements Wireable
{
    use WireableData;

    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        protected $options,
    ) {
    }

    public static function fromModel(\Lunar\Models\Product $product): self
    {
        return new self(
            $product->id,
            $product->translate('name'),
            $product->translate('description'),
            $product->options,
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
