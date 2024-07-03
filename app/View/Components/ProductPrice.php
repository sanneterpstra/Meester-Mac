<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Lunar\Facades\Pricing;
use Lunar\Models\Price;
use Lunar\Models\ProductVariant;

class ProductPrice extends Component
{
    public ?Price $price = null;

    public $purchasePrice = null;

    public ?ProductVariant $variant = null;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($product = null, $variant = null)
    {
        $this->price = Pricing::for(
            $variant ?: $product->variants->first()
        )->get()->matched;

        $this->purchasePrice = ($variant->attribute_data['also_price'] ?? '');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.product-price');
    }
}
